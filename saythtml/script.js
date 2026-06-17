document.addEventListener("DOMContentLoaded", () => {
  const tabs = document.querySelectorAll(".tab-btn");
  const slides = document.querySelectorAll(".slide");
  const prevBtn = document.querySelector(".prev-btn");
  const nextBtn = document.querySelector(".next-btn");
  const dotsContainer = document.querySelector(".dots-container");

  let currentSlides = Array.from(slides); // Массив слайдов, которые видны сейчас (с учетом фильтра)
  let currentIndex = 0;

  // 1. Функция обновления слайдера (показ текущего слайда и активация точки)
  function updateSlider() {
    // Скрываем вообще все слайды
    slides.forEach(slide => slide.classList.remove("active"));
    
    if (currentSlides.length > 0) {
      // Показываем текущий слайд из отфильтрованных
      currentSlides[currentIndex].classList.add("active");
    }

    // Обновляем активную точку
    const dots = document.querySelectorAll(".dot");
    dots.forEach((dot, idx) => {
      dot.classList.toggle("active", idx === currentIndex);
    });
  }

  // 2. Функция создания точек
  function createDots() {
    dotsContainer.innerHTML = ""; // Очищаем старые точки
    currentSlides.forEach((_, idx) => {
      const dot = document.createElement("div");
      dot.classList.add("dot");
      if (idx === 0) dot.classList.add("active");
      dot.addEventListener("click", () => {
        currentIndex = idx;
        updateSlider();
      });
      dotsContainer.appendChild(dot);
    });
  }
  
  tabs.forEach(tab => {
    tab.addEventListener("click", () => {
      // Меняем активный класс у кнопок табов
      tabs.forEach(t => t.classList.remove("active"));
      tab.classList.add("active");

      const filter = tab.getAttribute("data-filter");

      // Фильтруем слайды
      if (filter === "all") {
        currentSlides = Array.from(slides);
      } else {
        currentSlides = Array.from(slides).filter(slide => slide.getAttribute("data-category") === filter);
      }

      currentIndex = 0; // Сбрасываем индекс на первый слайд в новой категории
      createDots();     // Перерисовываем точки
      updateSlider();   // Обновляем показ
    });
  });

  // 4. Кнопки «Вперед» и «Назад»
  nextBtn.addEventListener("click", () => {
    if (currentSlides.length === 0) return;
    currentIndex = (currentIndex + 1) % currentSlides.length; // Идем по кругу вперед
    updateSlider();
  });

  prevBtn.addEventListener("click", () => {
    if (currentSlides.length === 0) return;
    currentIndex = (currentIndex - 1 + currentSlides.length) % currentSlides.length; // Идем по кругу назад
    updateSlider();
  });

  // Инициализация при первой загрузке страницы
  createDots();
  updateSlider();
});