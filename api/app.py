"""
API Flask da Estante Virtual.

Usa o mesmo banco do painel PHP (estante_virtual / mensagens_contato).

Rotas:
    GET  /api/mensagens  -> lista todas as mensagens (JSON)
    POST /api/mensagens  -> cadastra uma nova mensagem (JSON)

Como rodar:
    pip install -r requirements.txt
    python app.py
"""
import os
import re

import pymysql
from flask import Flask, jsonify, request
from flask_cors import CORS
from pymysql.cursors import DictCursor

app = Flask(__name__)
app.json.ensure_ascii = False  # mantém acentos legíveis no JSON

# Habilita CORS: permite que a página HTML (servida em outra origem/porta,
# ex.: http://localhost ou http://127.0.0.1:5500) chame esta API.
CORS(app)

# Mesmos dados de php/conexao.php (padrão do XAMPP). Podem ser trocados por
# variáveis de ambiente sem mexer no código.
DB_CONFIG = {
    "host": os.getenv("DB_HOST", "localhost"),
    "user": os.getenv("DB_USER", "root"),
    "password": os.getenv("DB_PASSWORD", ""),
    "database": os.getenv("DB_NAME", "estante_virtual"),
    "charset": "utf8mb4",
    "cursorclass": DictCursor,
}

EMAIL_REGEX = re.compile(r"^[^@\s]+@[^@\s]+\.[^@\s]+$")


def get_conexao():
    return pymysql.connect(**DB_CONFIG)


def serializar(linha):
    """Converte uma linha do banco em dict pronto para virar JSON."""
    return {
        "id": linha["id"],
        "nome": linha["nome"],
        "email": linha["email"],
        "mensagem": linha["mensagem"],
        # ISO 8601 sem fuso (ex.: 2026-10-22T14:30:00) -> o JS lê como hora local
        "data_envio": linha["data_envio"].isoformat() if linha["data_envio"] else None,
    }


def validar(dados):
    """Retorna uma mensagem de erro (str) ou None se os dados estiverem ok."""
    if not isinstance(dados, dict):
        return "Envie um objeto JSON com nome, email e mensagem."

    for campo in ("nome", "email", "mensagem"):
        valor = dados.get(campo)
        if not isinstance(valor, str) or not valor.strip():
            return f"O campo '{campo}' é obrigatório."

    if len(dados["nome"].strip()) > 100:
        return "O nome deve ter no máximo 100 caracteres."
    if len(dados["email"].strip()) > 100:
        return "O e-mail deve ter no máximo 100 caracteres."
    if not EMAIL_REGEX.match(dados["email"].strip()):
        return "Informe um e-mail válido."
    return None


@app.get("/api/mensagens")
def listar_mensagens():
    try:
        conexao = get_conexao()
        try:
            with conexao.cursor() as cursor:
                cursor.execute(
                    "SELECT id, nome, email, mensagem, data_envio "
                    "FROM mensagens_contato ORDER BY data_envio DESC, id DESC"
                )
                linhas = cursor.fetchall()
        finally:
            conexao.close()
    except pymysql.MySQLError as e:
        app.logger.error("Erro ao listar mensagens: %s", e)
        return jsonify({"erro": "Erro ao acessar o banco de dados."}), 500

    return jsonify([serializar(l) for l in linhas]), 200


@app.post("/api/mensagens")
def cadastrar_mensagem():
    dados = request.get_json(silent=True)  # None se o corpo não for JSON válido

    erro = validar(dados)
    if erro:
        return jsonify({"erro": erro}), 400

    nome = dados["nome"].strip()
    email = dados["email"].strip()
    mensagem = dados["mensagem"].strip()

    try:
        conexao = get_conexao()
        try:
            with conexao.cursor() as cursor:
                cursor.execute(
                    "INSERT INTO mensagens_contato (nome, email, mensagem) "
                    "VALUES (%s, %s, %s)",
                    (nome, email, mensagem),
                )
                novo_id = cursor.lastrowid
                conexao.commit()

                cursor.execute(
                    "SELECT id, nome, email, mensagem, data_envio "
                    "FROM mensagens_contato WHERE id = %s",
                    (novo_id,),
                )
                criada = cursor.fetchone()
        finally:
            conexao.close()
    except pymysql.MySQLError as e:
        app.logger.error("Erro ao cadastrar mensagem: %s", e)
        return jsonify({"erro": "Erro ao salvar no banco de dados."}), 500

    return jsonify(serializar(criada)), 201


if __name__ == "__main__":
    app.run(host="127.0.0.1", port=5000, debug=True)
