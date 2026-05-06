const counters = document.querySelectorAll(".counter");
const speed = 200;

const startCounters = (counter) => {
  // Đưa số về 0 trước khi chạy lại
  counter.innerText = "0";

  const updateCount = () => {
    const target = +counter.getAttribute("data-target");
    const count = +counter.innerText;
    const inc = target / speed;

    if (count < target) {
      counter.innerText = Math.ceil(count + inc);
      setTimeout(updateCount, 15);
    } else {
      counter.innerText = target;
    }
  };
  updateCount();
};

const observer = new IntersectionObserver(
  (entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        // Khi phần tử xuất hiện trong tầm mắt, kích hoạt chạy số
        startCounters(entry.target);
      }
    });
  },
  { threshold: 0.5 },
); // Chạy khi thấy được 50% khối thành tích

// Gán observer cho từng con số
counters.forEach((counter) => {
  observer.observe(counter);
});
