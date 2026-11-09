document.addEventListener("DOMContentLoaded", () => {
  const linksNav = document.querySelectorAll("nav a");
  const urlAtual = window.location.pathname;

  linksNav.forEach((link) => {
    const href = link.getAttribute("href");
    if (urlAtual.endsWith(href) || (href === "index.html" && urlAtual.endsWith("/"))) {
      link.classList.add("ativo");
    }
  });

  // --- Busca de livros (API Google Books) ---
  const botaoBusca = document.querySelector("#btn-busca");
  const inputBusca = document.querySelector("#input-busca");

  if (botaoBusca && inputBusca) {
    botaoBusca.addEventListener("click", () => buscarLivros(inputBusca.value));
    inputBusca.addEventListener("keydown", (e) => {
      if (e.key === "Enter") buscarLivros(inputBusca.value);
    });
  }
});

const API_LIVROS = "https://www.googleapis.com/books/v1/volumes";
const CAPA_PADRAO =
  "data:image/svg+xml;utf8," +
  encodeURIComponent(
    '<svg xmlns="http://www.w3.org/2000/svg" width="200" height="250">' +
    '<rect width="100%" height="100%" fill="#e8f4fc"/>' +
    '<text x="50%" y="50%" text-anchor="middle" font-family="sans-serif" ' +
    'font-size="16" fill="#2980b9">Sem capa</text></svg>'
  );

function mostrarMensagem(texto, tipo) {
  const msg = document.querySelector("#mensagem-busca");
  msg.textContent = texto;
  msg.className = "mensagem-busca " + (tipo || "");
  msg.hidden = texto === "";
}

function criarCardLivro(item) {
  const info = item.volumeInfo || {};
  const autores = info.authors ? info.authors.join(", ") : "Autor desconhecido";
  const capa = info.imageLinks && info.imageLinks.thumbnail
    ? info.imageLinks.thumbnail.replace("http://", "https://")
    : CAPA_PADRAO;

  const card = document.createElement("div");
  card.className = "card-livro";

  const img = document.createElement("img");
  img.src = capa;
  img.alt = "Capa do livro " + (info.title || "sem título");
  img.onerror = () => { img.src = CAPA_PADRAO; };

  const titulo = document.createElement("h3");
  titulo.textContent = info.title || "Sem título";

  const autor = document.createElement("p");
  autor.textContent = autores;

  card.append(img, titulo, autor);
  return card;
}

async function buscarLivros(termo) {
  const resultados = document.querySelector("#resultados-busca");
  termo = termo.trim();

  if (termo === "") {
    mostrarMensagem("Digite o nome de um livro ou autor para pesquisar.", "aviso");
    return;
  }

  resultados.innerHTML = "";
  mostrarMensagem("Buscando livros...", "info");

  try {
    const url = `${API_LIVROS}?q=${encodeURIComponent(termo)}&maxResults=8`;
    const resposta = await fetch(url);
    if (!resposta.ok) throw new Error("Erro HTTP " + resposta.status);

    const dados = await resposta.json();

    if (!dados.items || dados.items.length === 0) {
      mostrarMensagem(`Nenhum livro encontrado para "${termo}". Tente outro termo.`, "aviso");
      return;
    }

    dados.items.forEach((item) => resultados.appendChild(criarCardLivro(item)));
    mostrarMensagem(`${dados.items.length} resultado(s) para "${termo}":`, "sucesso");
  } catch (erro) {
    console.error("Falha na busca de livros:", erro);
    mostrarMensagem(
      "Não foi possível buscar os livros agora. Verifique sua conexão e tente novamente.",
      "erro"
    );
  }
}
