import os
import logging
from flask import Flask, request, jsonify
import requests
from dotenv import load_dotenv
from werkzeug.utils import secure_filename

load_dotenv()

logging.basicConfig(
    level=logging.INFO,
    format="%(asctime)s [%(levelname)s] %(message)s",
    handlers=[
        logging.FileHandler("logs/app.log"),
        logging.StreamHandler()
    ]
)

app = Flask(__name__)

OPENAI_API_KEY = os.getenv("OPENAI_API_KEY")

if not OPENAI_API_KEY:
    logging.error("OPENAI_API_KEY не найден")
    raise ValueError("Необходимо установить переменную окружения OPENAI_API_KEY")

CHATGPT_API_URL = "http://185.164.163.152/chat/completions"
WHISPER_API_URL = "http://185.164.163.152/audio/transcriptions"
IMAGE_ANALYSIS_API_URL = "http://185.164.163.152/image/analysis"


@app.route('/chat', methods=['POST'])
def chat():
    user_message = request.json.get('message')
    request_type = request.json.get('type')

    if not user_message or not request_type:
        logging.error("No message or type provided")
        return jsonify({'error': 'No message or type provided'}), 400

    headers = {
        "Content-Type": "application/json",
        "Authorization": f"Bearer {OPENAI_API_KEY}"
    }

    messages = [{"role": "user", "content": user_message}]
    if request_type == "summary":
        messages.insert(0, {"role": "system", "content": "Сделай саммари на основе данной информации."})
    elif request_type == "paraphrase":
        messages.insert(0, {"role": "system", "content": "Перефразируй предоставленную информацию."})
    elif request_type == "relevant_answers":
        messages.insert(0, {"role": "system", "content": "Выведи два наиболее релевантных ответа на основе информации."})
    else:
        return jsonify({'error': 'Invalid request type'}), 400

    data = {
        "model": "gpt-3.5-turbo",
        "messages": messages,
        "functions": [
            {"name": "get_relevant_answers", "parameters": {"num_answers": 2}}
        ] if request_type == "relevant_answers" else {}
    }

    response = requests.post(CHATGPT_API_URL, headers=headers, json=data)

    if response.status_code == 200:
        try:
            assistant_response = response.json().get('choices')[0]['message']['content']
            return jsonify({'text': assistant_response})
        except KeyError as e:
            logging.error(f"Invalid API response: {str(e)}")
            return jsonify({'error': 'Invalid API response', 'details': str(e)}), 500
    else:
        logging.error(f"Request failed: {response.text}")
        return jsonify({'error': 'Request failed', 'details': response.text}), response.status_code


@app.route('/transcribe', methods=['POST'])
def transcribe():
    if 'file' not in request.files:
        logging.error("No file provided")
        return jsonify({'error': 'No file provided'}), 400

    uploaded_file = request.files['file']
    message_context = request.form.get('message', '')
    filename = secure_filename(uploaded_file.filename)

    file_extension = filename.split('.')[-1].lower()

    if file_extension in ['wav']:
        uploaded_file.save(filename)

        headers = {
            "Authorization": f"Bearer {OPENAI_API_KEY}"
        }

        files = {
            'file': (filename, open(filename, 'rb'), 'application/octet-stream')
        }

        data = {
            'model': 'whisper-1'
        }

        response = requests.post(WHISPER_API_URL, headers=headers, files=files, data=data)

        os.remove(filename)

        if response.status_code == 200:
            try:
                transcription = response.json().get('text', '')
                if message_context:
                    return chat_with_context(transcription, message_context)
                else:
                    return jsonify({'text': transcription})
            except KeyError as e:
                logging.error(f"Invalid API response: {str(e)}")
                return jsonify({'error': 'Invalid API response', 'details': str(e)}), 500
        else:
            logging.error(f"Transcription failed: {response.text}")
            return jsonify({'error': 'Transcription failed', 'details': response.text}), response.status_code

    elif file_extension in ['jpg', 'jpeg', 'png']:
        uploaded_file.save(filename)

        headers = {
            "Authorization": f"Bearer {OPENAI_API_KEY}"
        }

        files = {
            'file': (filename, open(filename, 'rb'), 'application/octet-stream')
        }

        data = {
            'message': message_context
        }

        response = requests.post(IMAGE_ANALYSIS_API_URL, headers=headers, files=files, data=data)

        os.remove(filename)

        if response.status_code == 200:
            try:
                analysis_result = response.json().get('text', '')
                if message_context:
                    return chat_with_context(analysis_result, message_context)
                else:
                    return jsonify({'text': analysis_result})
            except KeyError as e:
                logging.error(f"Invalid API response: {str(e)}")
                return jsonify({'error': 'Invalid API response', 'details': str(e)}), 500
        else:
            logging.error(f"Image analysis failed: {response.text}")
            return jsonify({'error': 'Image analysis failed', 'details': response.text}), response.status_code

    else:
        logging.error("Unsupported file type")
        return jsonify({'error': 'Unsupported file type'}), 400


def chat_with_context(transcription, context):
    headers = {
        "Content-Type": "application/json",
        "Authorization": f"Bearer {OPENAI_API_KEY}"
    }

    messages = [
        {"role": "system", "content": "Сделай саммари на основе данной информации."},
        {"role": "user", "content": context},
        {"role": "user", "content": transcription}
    ]

    data = {
        "model": "gpt-3.5-turbo",
        "messages": messages
    }

    response = requests.post(CHATGPT_API_URL, headers=headers, json=data)

    if response.status_code == 200:
        try:
            assistant_response = response.json()['choices'][0]['message']['content']
            return jsonify({'text': assistant_response})
        except KeyError as e:
            logging.error(f"Invalid API response: {str(e)}")
            return jsonify({'error': 'Invalid API response', 'details': str(e)}), 500
    else:
        logging.error(f"Request failed: {response.text}")
        return jsonify({'error': 'Request failed', 'details': response.text}), response.status_code


if __name__ == '__main__':
    app.run(host='0.0.0.0', port=3002, debug=True)
