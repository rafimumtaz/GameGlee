const words = ["Coba cari Game Xbox", "Coba cari Console Nintendo", "Coba cari Game Card", "Coba cari Controller"];
    let currentWordIndex = 0;
    const dynamicInput = document.querySelector('.dynamic-text input'); // Select the input field
    const animatedTextElement = document.querySelector('.animated-text');
    let animationInterval; // To keep track of the animation interval
    let isUserTyping = false; // Flag to indicate if the user is currently typing

    // Function to change the animated text
    function changeWord() {
      const currentWord = words[currentWordIndex];
      let charIndex = 0;

      // Clear the previous animation if it exists
      if (animationInterval) {
        clearInterval(animationInterval);
      }

      animationInterval = setInterval(() => {
        animatedTextElement.textContent += currentWord[charIndex]; // Update animated text
        charIndex++;

        if (charIndex === currentWord.length) {
          clearInterval(animationInterval);
          setTimeout(() => {
            animatedTextElement.textContent = ''; // Clear animated text
            currentWordIndex = (currentWordIndex + 1) % words.length;
            changeWord(); // Change word after delay
          }, 1000);
        }
      }, 99); // Typing speed
    }

    // Start the animation
    changeWord();

    // Add event listener to the input
    dynamicInput.addEventListener('input', () => {
      // Stop the animated text when typing starts
      clearInterval(animationInterval);
      animatedTextElement.textContent = ''; // Clear animated text
      isUserTyping = true; // Set flag to true when typing
    });

    // Add event listener to handle input deletion
    dynamicInput.addEventListener('keydown', (event) => {
      if (event.key === 'Backspace') {
        if (dynamicInput.value.length === 1) {
          // If the input is empty after backspace, restart animation
          isUserTyping = false; // Reset flag
          animatedTextElement.textContent = ''; // Clear animated text
          changeWord(); // Restart animation
        }
      } else if (event.key === 'Enter') {
        // Keep the input value when Enter is pressed
        console.log('Searching for:', dynamicInput.value); // Log the search term (replace with your search logic)
        isUserTyping = false; // Reset flag
      }
    });

    // Add event listener to handle search button click
    document.querySelector('.search-box').addEventListener('click', () => {
      console.log('Searching for:', dynamicInput.value); // Log the search term (replace with your search logic)
      isUserTyping = false; // Reset flag
    });

    // Add event listener to close button
    document.querySelector('.close-button').addEventListener('click', () => {
      dynamicInput.value = ''; // Clear the input field
      animatedTextElement.textContent = ''; // Clear animated text
      isUserTyping = false; // Reset flag
      clearInterval(animationInterval); // Stop animation
      currentWordIndex = 0; // Reset to the first word
      changeWord(); // Restart animation
      console.log('Pencarian dihentikan'); // Indicate search stopped
    });

    // Restart animation when the user is not typing
    setInterval(() => {
      if (!isUserTyping) {
        animatedTextElement.textContent = ''; // Clear current animated text
        changeWord(); // Restart animation
      }
    }, 9000); // Restart animation every 2 seconds if not typing
   let currentIndex = 1; // Indeks slide saat ini
const slides = document.querySelectorAll('.slide'); // Semua elemen dengan kelas .slide
const dots = document.querySelectorAll('.dot'); // Semua elemen dengan kelas .dot

// Fungsi untuk menampilkan slide sesuai dengan indeks
function showSlide(index) {
  // Melakukan pembungkusan indeks agar melingkar
  if (index > slides.length) currentIndex = 1; // Kembali ke slide pertama
  else if (index < 1) currentIndex = slides.length; // Kembali ke slide terakhir
  else currentIndex = index;

  // Perbarui status slide: tampilkan slide aktif dan sembunyikan lainnya
  slides.forEach((slide, i) => {
    slide.style.display = i === currentIndex - 1 ? "block" : "none"; // Hanya slide aktif yang terlihat
  });

  // Perbarui status dots: tambahkan kelas 'active' pada dot yang aktif
  dots.forEach((dot, i) => {
    dot.classList.toggle("active", i === currentIndex - 1);
  });
}

// Fungsi untuk mengubah slide berdasarkan tombol "prev" atau "next"
function changeSlide(n) {
  showSlide(currentIndex - n); // Tambahkan atau kurangi indeks, lalu tampilkan slide
}

// Fungsi untuk mengatur slide yang aktif berdasarkan klik pada dot
function currentSlide(n) {
  showSlide(n); // Tampilkan slide berdasarkan indeks
}

// Inisialisasi carousel dengan menampilkan slide pertama
showSlide(currentIndex);

// Tambahkan event listeners untuk klik pada dots
dots.forEach((dot, index) => {
  dot.addEventListener("click", () => {
    currentSlide(index + 1); // Set currentSlide sesuai indeks (1-based)
  });
});

// Tambahkan event listeners untuk tombol "prev" dan "next"
const prevButton = document.querySelector('.carousel-control.prev');
const nextButton = document.querySelector('.carousel-control.next');

// Fungsi untuk tombol Previous
if (prevButton) {
  prevButton.addEventListener('click', () => {
    changeSlide(-1); // Pindah ke slide sebelumnya
  });
}

// Fungsi untuk tombol Next
if (nextButton) {
  nextButton.addEventListener('click', () => {
    changeSlide(1); // Pindah ke slide berikutnya
  });
}

function openPopup() {
  document.getElementById("popupTambahKomunitas").style.display = "block";
}

function closePopup() {
  document.getElementById("popupTambahKomunitas").style.display = "none";
}

// Event listener untuk tombol close
document.getElementById("closePopup").addEventListener("click", closePopup);
