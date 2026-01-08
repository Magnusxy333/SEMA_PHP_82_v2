const productContainers = [...document.querySelectorAll('.videos-container')];
const nxtBtn = [...document.querySelectorAll('.nxt-btn')];
const preBtn = [...document.querySelectorAll('.pre-btn')];

productContainers.forEach((item, i) => {
    let containerDimensions = item.getBoundingClientRect();
    let containerWidth = containerDimensions.width;

    nxtBtn[i].addEventListener('click', () => {
        item.scrollLeft += containerWidth;
    })

    preBtn[i].addEventListener('click', () => {
        item.scrollLeft -= containerWidth;
    })
})

document.addEventListener("DOMContentLoaded", function() {
          // 1. Seleciona os cards que serão animados
          const cardsToAnimate = document.querySelectorAll('.card1, .card2');

          // 2. Opções do observador (Dispara quando 10% do elemento entra na tela)
          const observerOptions = {
              threshold: 0.1, 
              rootMargin: '0px 0px -50px 0px' // Opcional: Aciona um pouco antes de o elemento tocar a parte inferior da tela
          };

          // 3. Função de Callback do Observador
          const observerCallback = (entries, observer) => {
              entries.forEach(entry => {
                  // Verifica se o elemento está visível
                  if (entry.isIntersecting) {
                      const element = entry.target;
                      
                      // Verifica qual card é para aplicar o atraso
                      if (element.classList.contains('card2')) {
                          // Atraso para o CARD 2 (simulando animation-delay: 0.2s)
                          setTimeout(() => {
                              element.classList.add('animate-on-scroll');
                          }, 200); 
                      } else {
                          // CARD 1 anima imediatamente
                          element.classList.add('animate-on-scroll');
                      }
                      
                      // Para que a animação só rode uma vez, pare de observar o elemento
                      observer.unobserve(element);
                  }
              });
          };

          // 4. Cria o observador
          const observer = new IntersectionObserver(observerCallback, observerOptions);

          // 5. Começa a observar cada card
          cardsToAnimate.forEach(card => {
              observer.observe(card);
          });
      });