FROM python:3.9-slim

RUN apt-get update && apt-get install -y \
    ffmpeg \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /app

COPY requirements.txt ./

RUN pip install --no-cache-dir -r requirements.txt

COPY . .

EXPOSE 3002

CMD ["gunicorn", "--bind", "0.0.0.0:3002", "--timeout", "600", "--access-logfile", "-", "--error-logfile", "-", "run:app"]
