// Gallery functionality
'use strict';

const galleryImages = [];
let currentImageIndex = 0;
let touchStartX = 0;
let touchEndX = 0;

// Initialize gallery on page load
document.addEventListener('DOMContentLoaded', function() {
  try {
    // Get all thumbnail images
    const thumbs = document.querySelectorAll('.gallery-thumb img');
    thumbs.forEach(thumb => {
      const src = thumb.src.replace('w=200', 'w=1200');
      galleryImages.push(src);
    });
    
    // Initialize touch support
    initTouchSupport();
    
    updateGalleryDots();
    
    console.log('Gallery JS loaded successfully');
  } catch (error) {
    console.error('Error initializing gallery:', error);
  }
});

// Initialize touch/swipe support
function initTouchSupport() {
  const gallery = document.getElementById('mainGallery');
  if (gallery) {
    gallery.addEventListener('touchstart', handleTouchStart, { passive: true });
    gallery.addEventListener('touchend', handleTouchEnd, { passive: true });
  }
}

function handleTouchStart(e) {
  touchStartX = e.changedTouches[0].screenX;
}

function handleTouchEnd(e) {
  touchEndX = e.changedTouches[0].screenX;
  handleSwipe();
}

function setImage(index) {
  if (galleryImages.length === 0) return;
  
  if (index < 0) index = galleryImages.length - 1;
  if (index >= galleryImages.length) index = 0;
  
  currentImageIndex = index;
  
  const mainImage = document.getElementById('mainImage');
  if (mainImage && galleryImages[index]) {
    // Add loading state
    mainImage.style.opacity = '0.7';
    mainImage.src = galleryImages[index];
    mainImage.onload = () => {
      mainImage.style.opacity = '1';
    };
  }
  
  // Update thumbnails
  const thumbs = document.querySelectorAll('.gallery-thumb');
  thumbs.forEach((thumb, i) => {
    thumb.classList.toggle('active', i === index);
  });
  
  // Preload adjacent images
  preloadImage(index + 1);
  preloadImage(index - 1);
  
  updateGalleryDots();
}

function nextImage() {
  setImage(currentImageIndex + 1);
}

function prevImage() {
  setImage(currentImageIndex - 1);
}

function updateGalleryDots() {
  const dots = document.querySelectorAll('.gallery-dot');
  dots.forEach((dot, i) => {
    dot.classList.toggle('active', i === currentImageIndex);
  });
}

// Keyboard navigation
document.addEventListener('keydown', function(e) {
  // Only handle if gallery exists and is visible
  const gallery = document.getElementById('mainGallery');
  if (!gallery) return;
  
  if (e.key === 'ArrowLeft') {
    e.preventDefault();
    prevImage();
  }
  if (e.key === 'ArrowRight') {
    e.preventDefault();
    nextImage();
  }
});

// Handle swipe gesture
function handleSwipe() {
  const swipeThreshold = 50;
  const diff = touchStartX - touchEndX;
  
  if (Math.abs(diff) > swipeThreshold) {
    if (diff > 0) {
      nextImage();
    } else {
      prevImage();
    }
  }
}

// Preload adjacent images for smoother navigation
function preloadImage(index) {
  if (index >= 0 && index < galleryImages.length) {
    const img = new Image();
    img.src = galleryImages[index];
  }
}
