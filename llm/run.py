import os
import json
import base64
import logging
from flask import Flask, request, jsonify
from datetime import timedelta
import requests
from dotenv import load_dotenv
from werkzeug.utils import secure_filename
from pydub import AudioSegment

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
app.config['PERMANENT_SESSION_LIFETIME'] = timedelta(minutes=30)

OPENAI_API_KEY = os.getenv("OPENAI_API_KEY")
HF_AUTH_TOKEN = os.getenv("HF_AUTH_TOKEN")
MODEL_NAME = "llama3"

CHATGPT_API_URL = "http://185.164.163.152/chat/completions"
WHISPER_API_URL = "http://185.164.163.152/audio/transcriptions"
IMAGE_ANALYSIS_API_URL = "http://185.164.163.152/image/analysis"

if not OPENAI_API_KEY:
    logging.error("OPENAI_API_KEY не найден")
    raise ValueError("Необходимо установить переменную окружения OPENAI_API_KEY")


@app.route('/summary', methods=['POST'])
def summary():
    try:
        data = request.json
        if not data or 'patient_data' not in data:
            logging.error("Отсутствуют данные пациента.")
            return jsonify({"error": "No patient data provided"}), 400

        patient_data = data['patient_data']
        if not isinstance(patient_data, str) or not patient_data.strip():
            logging.error("Неверный формат данных пациента.")
            return jsonify({"error": "Invalid patient data format"}), 400

        prompt = f"""
        Анализируй данные пациента:
        {patient_data}

        Найди изменения и представь результат в формате:
        "Давление изменилось с [значение 1] на [значение 2]."
        Ответь на русском.пше фв
        """

        response = requests.post(
            "http://ollama:11434/api/generate",
            json={"model": MODEL_NAME, "prompt": prompt, "stream": False},
            timeout=300
        )

        logging.info(f"Raw response: {response.text}")

        if response.status_code != 200:
            logging.error(f"Ошибка API Ollama: {response.text}")
            return jsonify({"error": "Failed to process data with model"}), 500

        try:
            result = response.json()
            logging.info(f"Parsed JSON: {result}")
            summary = result.get("response", "")
            if not summary.strip():
                logging.error("Пустой ответ от модели.")
                return jsonify({"summary": ""}), 200

            return jsonify({"summary": summary})

        except json.JSONDecodeError as e:
            logging.error(f"Ошибка при парсинге JSON: {e}")
            return jsonify({"error": "Invalid JSON response from Ollama"}), 500

    except Exception as e:
        logging.error(f"Ошибка обработки: {e}")
        return jsonify({"error": str(e)}), 500

@app.route('/chat', methods=['POST'])
def chat():
    import json
    import logging
    from flask import request, jsonify

    user_message = request.json.get('message')
    request_type = request.json.get('type')

    if not user_message or not request_type:
        return jsonify({'error': 'No message or type provided'}), 400

    headers = {
        "Content-Type": "application/json",
        "Authorization": f"Bearer {OPENAI_API_KEY}"
    }

    messages = [{"role": "user", "content": user_message}]
    if request_type == "summary":
        messages.insert(0, {"role": "system", "content": "Сделай краткое описание предоставленного текста."})
    elif request_type == "paraphrase":
        messages.insert(0, {"role": "system", "content": "Перефразируй текст, сохранив его смысл."})
    elif request_type == "relevant_answers":
        messages.insert(0, {"role": "system", "content": "Предоставь два наиболее релевантных ответа в аргументах функции."})
    else:
        return jsonify({'error': 'Invalid request type'}), 400

    function_data = [
        {
            "name": "get_relevant_answers",
            "description": "Возвращает два наиболее релевантных ответа в аргументах функции.",
            "parameters": {
                "type": "object",
                "properties": {
                    "answer1": {
                        "type": "string",
                        "description": "Первый релевантный ответ"
                    },
                    "answer2": {
                        "type": "string",
                        "description": "Второй релевантный ответ"
                    }
                },
                "required": ["answer1", "answer2"]
            }
        }
    ] if request_type == "relevant_answers" else None
    data = {
        "model": "gpt-3.5-turbo",
        "messages": messages,
        "max_tokens": 300,
        "temperature": 0.7
    }

    if request_type == "relevant_answers":
        data["functions"] = function_data
        data["function_call"] = {"name": "get_relevant_answers"}

    try:
        response = requests.post(CHATGPT_API_URL, headers=headers, json=data)

        if response.status_code == 200:
            response_json = response.json()
            logging.info(f"API Response: {response_json}")

            if request_type == "relevant_answers":
                choice = response_json.get("choices", [{}])[0]
                function_call_data = choice.get("message", {}).get("function_call", {})
                arguments = function_call_data.get("arguments")

                if arguments:
                    structured_args = json.loads(arguments)
                    answer1 = structured_args.get("answer1")
                    answer2 = structured_args.get("answer2")

                    if answer1 and answer2:
                        return jsonify({"text": [answer1, answer2]})
                    else:
                        return jsonify({"error": "Missing answers in function response", "details": structured_args}), 500
                else:
                    return jsonify({"error": "No arguments in function_call"}), 500

            choice = response_json.get("choices", [{}])[0]
            content = choice.get("message", {}).get("content")
            if content:
                return jsonify({"text": content})
            else:
                return jsonify({"error": "No content in response"}), 500
        else:
            return jsonify({"error": "Request to OpenAI API failed", "details": response.text}), 500
    except Exception as e:
        logging.error(f"Unhandled error: {e}")
        return jsonify({"error": "Internal server error", "details": str(e)}), 500

@app.route('/transcribe', methods=['POST'])
def transcribe():
    if 'file' not in request.files:
        return jsonify({'error': 'No file provided'}), 400

    uploaded_file = request.files['file']
    filename = secure_filename(uploaded_file.filename)
    file_extension = filename.split('.')[-1].lower()

    # Поддерживаемые форматы
    supported_audio_formats = ['wav', 'oga', 'mp3', 'mp4', 'mpeg', 'mpga', 'webm']
    supported_image_formats = ['jpg', 'jpeg', 'png']

    # Обработка аудио
    if file_extension in supported_audio_formats:
        uploaded_file.save(filename)

        wav_filename = filename
        if file_extension == "oga":
            wav_filename = filename.replace(".oga", ".wav")
            convert_to_wav(filename, wav_filename)
            os.remove(filename)

        try:
            with open(wav_filename, 'rb') as audio_file:
                headers = {"Authorization": f"Bearer {OPENAI_API_KEY}"}
                files = {'file': (wav_filename, audio_file, 'application/octet-stream')}
                data = {'model': 'whisper-1'}

                response = requests.post(WHISPER_API_URL, headers=headers, files=files, data=data)

            os.remove(wav_filename)

            if response.status_code == 200:
                transcription = response.json().get('text', '')
                return jsonify({'transcription': transcription})
            else:
                return jsonify({'error': 'Transcription failed', 'details': response.text}), response.status_code

        except Exception as e:
            os.remove(wav_filename)
            return jsonify({'error': 'Internal server error', 'details': str(e)}), 500

    # Обработка изображений
    elif file_extension in supported_image_formats:
        uploaded_file.save(filename)

        try:
            base64_image = encode_image(filename)
            os.remove(filename)

            headers = {
                "Content-Type": "application/json",
                "Authorization": f"Bearer {OPENAI_API_KEY}"
            }

            data = {
                "model": "gpt-4o-mini",
                "messages": [
                    {
                        "role": "user",
                        "content": [
                            {"type": "text",
                             "text": "На изображении показан медицинский прибор. Определи его тип и значения его показателей."},
                            {"type": "image_url", "image_url": {"url": f"data:image/jpeg;base64,{base64_image}"}}
                        ]
                    }
                ],
                "max_tokens": 300
            }

            response = requests.post(CHATGPT_API_URL, headers=headers, json=data)

            if response.status_code == 200:
                raw_description = response.json()['choices'][0]['message']['content']

                extracted_values = extract_values_from_text(raw_description)

                return jsonify({'transcription': extracted_values})
            else:
                return jsonify({'error': 'Image analysis failed', 'details': response.text}), response.status_code

        except Exception as e:
            return jsonify({'error': 'Internal server error', 'details': str(e)}), 500

    else:
        return jsonify({'error': 'Unsupported file type'}), 400



@app.route('/transcribe-parser', methods=['POST'])
def transcribe_parser():
    if 'file' not in request.files:
        return jsonify({'error': 'No file provided'}), 400

    uploaded_file = request.files['file']
    message_context = request.form.get('message', '')
    filename = secure_filename(uploaded_file.filename)
    file_extension = filename.split('.')[-1].lower()

    supported_audio_formats = ['wav', 'oga', 'mp3', 'mp4', 'mpeg', 'mpga', 'webm']
    supported_image_formats = ['jpg', 'jpeg', 'png']

    if file_extension in supported_audio_formats:
        uploaded_file.save(filename)

        wav_filename = filename
        if file_extension == "oga":
            wav_filename = filename.replace(".oga", ".wav")
            convert_to_wav(filename, wav_filename)
            os.remove(filename)

        try:
            with open(wav_filename, 'rb') as audio_file:
                headers = {"Authorization": f"Bearer {OPENAI_API_KEY}"}
                files = {'file': (wav_filename, audio_file, 'application/octet-stream')}
                data = {'model': 'whisper-1'}

                response = requests.post(WHISPER_API_URL, headers=headers, files=files, data=data)

            os.remove(wav_filename)

            if response.status_code == 200:
                transcription = response.json().get('text', '')

                if message_context:
                    description = f"{message_context}\n\n{transcription}"
                else:
                    description = transcription

                # Используем GPT для извлечения ключевых значений
                value = extract_values_from_text(description)

                return jsonify({'description': description, 'value': value})
            else:
                return jsonify({'error': 'Transcription failed', 'details': response.text}), response.status_code

        except Exception as e:
            os.remove(wav_filename)
            return jsonify({'error': 'Internal server error', 'details': str(e)}), 500

    elif file_extension in supported_image_formats:
        uploaded_file.save(filename)

        try:
            base64_image = encode_image(filename)
            os.remove(filename)

            headers = {
                "Content-Type": "application/json",
                "Authorization": f"Bearer {OPENAI_API_KEY}"
            }

            data = {
                "model": "gpt-4o-mini",
                "messages": [
                    {
                        "role": "user",
                        "content": [
                            {"type": "text",
                             "text": "На изображении показан медицинский прибор. Определи его тип и значения его показателей. Если это не медицинский прибор, то просто опиши картинку, но нам важны показатели если они там есть."},
                            {"type": "image_url", "image_url": {"url": f"data:image/jpeg;base64,{base64_image}"}}
                        ]
                    }
                ],
                "max_tokens": 300
            }

            response = requests.post(CHATGPT_API_URL, headers=headers, json=data)

            if response.status_code == 200:
                description = response.json()['choices'][0]['message']['content']

                # Используем GPT для извлечения ключевых значений
                value = extract_values_from_text(description)

                return jsonify({'description': description, 'value': value})
            else:
                return jsonify({'error': 'Image analysis failed', 'details': response.text}), response.status_code

        except Exception as e:
            return jsonify({'error': 'Internal server error', 'details': str(e)}), 500

    else:
        return jsonify({'error': 'Unsupported file type'}), 400

def extract_values_from_text(text):
    """
    Вызывает GPT для выделения ключевых показателей из текста в компактном формате.
    """
    try:
        headers = {
            "Content-Type": "application/json",
            "Authorization": f"Bearer {OPENAI_API_KEY}"
        }

        data = {
            "model": "gpt-4",
            "messages": [
                {
                    "role": "user",
                    "content": (
                        f"Извлеки ключевые показатели из текста: '{text}'. "
                        "Если это артериальное давление, ответь в формате 'SYS/DIA, Pulse'. "
                        "Если это температура, ответь просто числом с °C. "
                        "Если прибор показывает другие данные, перечисли их в компактном российском формате."
                    )
                }
            ],
            "max_tokens": 100
        }

        response = requests.post(CHATGPT_API_URL, headers=headers, json=data)

        if response.status_code == 200:
            return response.json()['choices'][0]['message']['content'].strip()
        else:
            return "Не удалось извлечь показатели"
    except Exception as e:
        return f"Ошибка извлечения данных: {str(e)}"


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

def convert_to_wav(input_path, output_path):
    """Конвертирует аудиофайл в формат WAV."""
    audio = AudioSegment.from_file(input_path)
    audio.export(output_path, format="wav")

def encode_image(image_path):
    """Конвертирует изображение в Base64."""
    with open(image_path, "rb") as image_file:
        return base64.b64encode(image_file.read()).decode('utf-8')


if __name__ == '__main__':
    app.run(host='0.0.0.0', port=3002, debug=True)
