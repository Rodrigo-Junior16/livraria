// Consome a API Flask (api/app.py): lista (GET) e cadastra (POST) mensagens.
const API_MENSAGENS = "http://127.0.0.1:5000/api/mensagens";

document.addEventListener("DOMContentLoaded", () => {
  const form = document.querySelector("#form-mensagem");
  if (!form) return; // este script só faz algo na página de mensagens

  form.addEventListener("submit", cadastrarMensagem);
  carregarMensagens();
});

// ---------- Utilitários de tela ----------

function exibirStatus(texto, tipo) {
  const el = document.querySelector("#status-mensagens");
  el.textContent = texto;
  el.className = "mensagem-busca " + (tipo || "");
  el.hidden = texto === "";
}

function formatarData(iso) {
  if (!iso) return "-";
  const data = new Date(iso);
  if (isNaN(data)) return "-";
  return data.toLocaleString("pt-BR", { dateStyle: "short", timeStyle: "short" });
}

function criarLinha(msg) {
  const tr = document.createElement("tr");
  // textContent (e não innerHTML) evita injeção de HTML vindo do banco
  [msg.id, msg.nome, msg.email, msg.mensagem, formatarData(msg.data_envio)].forEach((valor) => {
    const td = document.createElement("td");
    td.textContent = valor;
    tr.appendChild(td);
  });
  return tr;
}

function renderizarMensagens(mensagens) {
  const tbody = document.querySelector("#lista-mensagens");
  tbody.innerHTML = "";

  if (mensagens.length === 0) {
    const tr = document.createElement("tr");
    const td = document.createElement("td");
    td.colSpan = 5;
    td.style.textAlign = "center";
    td.textContent = "Nenhuma mensagem registrada.";
    tr.appendChild(td);
    tbody.appendChild(tr);
    return;
  }

  mensagens.forEach((msg) => tbody.appendChild(criarLinha(msg)));
}

// Lê a mensagem de erro enviada pela API ({"erro": "..."}) quando houver
async function lerErro(resposta) {
  try {
    const corpo = await resposta.json();
    if (corpo && corpo.erro) return corpo.erro;
  } catch (_) {
    // corpo não era JSON: usa a mensagem padrão abaixo
  }
  return "Erro HTTP " + resposta.status;
}

// ---------- GET: lista os registros ----------

async function carregarMensagens() {
  try {
    const resposta = await fetch(API_MENSAGENS);
    if (!resposta.ok) throw new Error(await lerErro(resposta));

    const mensagens = await resposta.json();
    renderizarMensagens(mensagens);
  } catch (erro) {
    console.error("Falha ao carregar mensagens:", erro);
    document.querySelector("#lista-mensagens").innerHTML = "";
    exibirStatus(
      "Não foi possível carregar as mensagens. Verifique se a API Flask está rodando.",
      "erro"
    );
  }
}

// ---------- POST: cadastra um novo registro ----------

async function cadastrarMensagem(evento) {
  evento.preventDefault(); // impede o envio tradicional (que recarregaria a página)

  const form = evento.target;
  const botao = document.querySelector("#btn-salvar");

  const dados = {
    nome: form.nome.value.trim(),
    email: form.email.value.trim(),
    mensagem: form.mensagem.value.trim(),
  };

  if (!dados.nome || !dados.email || !dados.mensagem) {
    exibirStatus("Preencha nome, e-mail e mensagem.", "aviso");
    return;
  }

  botao.disabled = true;
  exibirStatus("Salvando...", "info");

  try {
    const resposta = await fetch(API_MENSAGENS, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(dados),
    });
    if (!resposta.ok) throw new Error(await lerErro(resposta));

    form.reset();
    exibirStatus("Mensagem cadastrada com sucesso!", "sucesso");
    await carregarMensagens(); // atualiza a lista sem recarregar a página
  } catch (erro) {
    console.error("Falha ao cadastrar mensagem:", erro);
    // TypeError = falha de rede/CORS (API fora do ar); demais = erro devolvido pela API
    const msg = erro instanceof TypeError
      ? "Não foi possível conectar à API. Verifique se o Flask está rodando."
      : erro.message;
    exibirStatus("Erro ao cadastrar: " + msg, "erro");
  } finally {
    botao.disabled = false;
  }
}
