import TitleAnimation from "./animations/TitleAnimation";
import ParagraphAnimation from "./animations/ParagraphAnimation";

(() => {
  const onReady = () => {
    const titles = document.querySelectorAll('[data-animate="title"]');

    titles.forEach((el) => {
      new TitleAnimation({ el });
    });

    const paragraphs = document.querySelectorAll('[data-animate="paragraph"]');

    paragraphs.forEach((el) => {
      new ParagraphAnimation({ el });
    });
  };

  if (document.readyState !== "loading") {
    onReady();
  } else {
    document.addEventListener("DOMContentLoaded", onReady);
  }
})();
