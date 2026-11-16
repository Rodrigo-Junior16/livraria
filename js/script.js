document.addEventListener("DOMContentLoaded", () => {
  const linksNav = document.querySelectorAll("nav a");
  const urlAtual = window.location.pathname;

  linksNav.forEach((link) => {
    const href = link.getAttribute("href");
    if (urlAtual.endsWith(href) || (href === "index.html" && urlAtual.endsWith("/"))) {
      link.classList.add("ativo");
    }
  });

  const botaoBusca = document.querySelector("#btn-busca");
  const inputBusca = document.querySelector("#input-busca");

  if (botaoBusca && inputBusca) {
    botaoBusca.addEventListener("click", () => {
      const termo = inputBusca.value.trim();
      if (termo !== "") {
        alert(`Buscando por: "${termo}"...`);
      } else {
        alert("Por favor, digite o nome de um livro ou autor para pesquisar.");
      }
    });
  }
});