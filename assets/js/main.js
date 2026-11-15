/**
 * GYPS LANKA - Main Frontend Script (2026 Animated Edition)
 */

document.addEventListener('DOMContentLoaded', function () {
    // ==============================================================
    // 1. MOBILE NAVIGATION TOGGLE
    // ==============================================================
    const mobileToggle = document.getElementById('mobileToggle');
    const navMenu = document.getElementById('navMenu');

    if (mobileToggle && navMenu) {
        mobileToggle.addEventListener('click', function () {
            navMenu.classList.toggle('open');
            const icon = mobileToggle.querySelector('i');
            if (icon) {
                if (navMenu.classList.contains('open')) {
                    icon.classList.remove('fa-bars');
                    icon.classList.add('fa-xmark');
                } else {
                    icon.classList.remove('fa-xmark');
                    icon.classList.add('fa-bars');
                }
            }
        });
    }

    // ==============================================================
    // 2. INTERACTIVE HERO IMAGE SLIDER (Background Edition)
    // ==============================================================
    const heroBgSlides = document.querySelectorAll('.hero-bg-slide');
    const heroSlides = document.querySelectorAll('.hero-slide');
    const heroDots = document.querySelectorAll('.hero-dot');
    const heroPrevBtn = document.getElementById('heroPrevBtn');
    const heroNextBtn = document.getElementById('heroNextBtn');
    const heroCounter = document.getElementById('heroCounter');
    const heroSliderSection = document.getElementById('heroSliderSection');

    let currentSlide = 0;
    const totalSlides = heroSlides.length;
    let autoSlideInterval = null;
    const slideDuration = 6000; // 6 seconds per slide

    function showSlide(index) {
        if (totalSlides === 0) return;

        // Wrap index
        if (index >= totalSlides) currentSlide = 0;
        else if (index < 0) currentSlide = totalSlides - 1;
        else currentSlide = index;

        // Update full-width background slides
        heroBgSlides.forEach((bg, idx) => {
            if (idx === currentSlide) {
                bg.classList.add('active');
            } else {
                bg.classList.remove('active');
            }
        });

        // Update text slides visibility
        heroSlides.forEach((slide, idx) => {
            if (idx === currentSlide) {
                slide.classList.add('active');
            } else {
                slide.classList.remove('active');
            }
        });

        // Update dots
        heroDots.forEach((dot, idx) => {
            if (idx === currentSlide) {
                dot.classList.add('active');
            } else {
                dot.classList.remove('active');
            }
        });

        // Update counter e.g. 01 / 04
        if (heroCounter) {
            heroCounter.textContent = `0${currentSlide + 1} / 0${totalSlides}`;
        }
    }

    function nextSlide() {
        showSlide(currentSlide + 1);
    }

    function prevSlide() {
        showSlide(currentSlide - 1);
    }

    function startAutoSlide() {
        stopAutoSlide();
        autoSlideInterval = setInterval(nextSlide, slideDuration);
    }

    function stopAutoSlide() {
        if (autoSlideInterval) {
            clearInterval(autoSlideInterval);
            autoSlideInterval = null;
        }
    }

    if (totalSlides > 0) {
        // Dot clicks
        heroDots.forEach((dot, idx) => {
            dot.addEventListener('click', function () {
                showSlide(idx);
                startAutoSlide();
            });
        });

        // Arrow button clicks
        if (heroPrevBtn) {
            heroPrevBtn.addEventListener('click', function () {
                prevSlide();
                startAutoSlide();
            });
        }

        if (heroNextBtn) {
            heroNextBtn.addEventListener('click', function () {
                nextSlide();
                startAutoSlide();
            });
        }

        // Pause on hover
        if (heroSliderSection) {
            heroSliderSection.addEventListener('mouseenter', stopAutoSlide);
            heroSliderSection.addEventListener('mouseleave', startAutoSlide);

            // Touch Swipe Support for Mobile
            let touchStartX = 0;
            let touchEndX = 0;

            heroSliderSection.addEventListener('touchstart', function (e) {
                touchStartX = e.changedTouches[0].screenX;
            }, { passive: true });

            heroSliderSection.addEventListener('touchend', function (e) {
                touchEndX = e.changedTouches[0].screenX;
                handleHeroSwipe();
            }, { passive: true });

            function handleHeroSwipe() {
                const diff = touchEndX - touchStartX;
                if (Math.abs(diff) > 40) {
                    if (diff < 0) {
                        nextSlide();
                    } else {
                        prevSlide();
                    }
                    startAutoSlide();
                }
            }
        }

        // Initialize slider
        showSlide(0);
        startAutoSlide();
    }

    // ==============================================================
    // 3. RECENT PROJECTS INTERACTIVE CAROUSEL
    // ==============================================================
    const projTrack = document.getElementById('projectsCarouselTrack');
    const projPrevBtn = document.getElementById('projPrevBtn');
    const projNextBtn = document.getElementById('projNextBtn');
    const projDots = document.querySelectorAll('.carousel-dot');

    if (projTrack) {
        const getCardWidth = () => {
            const firstCard = projTrack.querySelector('.project-card');
            return firstCard ? firstCard.offsetWidth + 22 : 300;
        };

        if (projNextBtn) {
            projNextBtn.addEventListener('click', function () {
                projTrack.scrollBy({ left: getCardWidth() * 1.5, behavior: 'smooth' });
            });
        }

        if (projPrevBtn) {
            projPrevBtn.addEventListener('click', function () {
                projTrack.scrollBy({ left: -getCardWidth() * 1.5, behavior: 'smooth' });
            });
        }

        // Update dots on scroll
        projTrack.addEventListener('scroll', function () {
            const scrollPos = projTrack.scrollLeft;
            const maxScroll = projTrack.scrollWidth - projTrack.clientWidth;
            if (maxScroll <= 0) return;

            const progress = scrollPos / maxScroll;
            const activeDotIndex = Math.min(
                projDots.length - 1,
                Math.floor(progress * projDots.length)
            );

            projDots.forEach((dot, idx) => {
                if (idx === activeDotIndex) {
                    dot.classList.add('active');
                } else {
                    dot.classList.remove('active');
                }
            });
        });

        // Click dots to scroll to ratio
        projDots.forEach((dot, idx) => {
            dot.addEventListener('click', function () {
                const maxScroll = projTrack.scrollWidth - projTrack.clientWidth;
                const targetScroll = (maxScroll / (projDots.length - 1)) * idx;
                projTrack.scrollTo({ left: targetScroll, behavior: 'smooth' });
            });
        });
    }

    // ==============================================================
    // 4. SCROLL REVEAL ANIMATIONS (IntersectionObserver)
    // ==============================================================
    const revealElements = document.querySelectorAll('.reveal-on-scroll');
    if ('IntersectionObserver' in window && revealElements.length > 0) {
        const revealObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-revealed');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            root: null,
            threshold: 0.12,
            rootMargin: '0px 0px -40px 0px'
        });

        revealElements.forEach(el => revealObserver.observe(el));
    } else {
        // Fallback for older browsers
        revealElements.forEach(el => el.classList.add('is-revealed'));
    }

    // ==============================================================
    // 5. ANIMATED STATS NUMBER COUNTER
    // ==============================================================
    const counters = document.querySelectorAll('.stat-number');
    if ('IntersectionObserver' in window && counters.length > 0) {
        const countObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const counter = entry.target;
                    const target = parseInt(counter.getAttribute('data-target') || '0', 10);
                    const suffix = counter.getAttribute('data-suffix') || '';
                    let count = 0;
                    const speed = target > 100 ? 20 : 60;
                    const increment = Math.max(1, Math.ceil(target / 40));

                    const updateCount = () => {
                        count += increment;
                        if (count < target) {
                            counter.textContent = count + suffix;
                            setTimeout(updateCount, speed);
                        } else {
                            counter.textContent = target + suffix;
                        }
                    };
                    updateCount();
                    observer.unobserve(counter);
                }
            });
        }, { threshold: 0.5 });

        counters.forEach(c => countObserver.observe(c));
    }

    // ==============================================================
    // 6. FREE QUOTE MODAL HANDLERS
    // ==============================================================
    const quoteModal = document.getElementById('quoteModal');
    const quoteButtons = document.querySelectorAll('.open-quote-modal');
    const closeModal = document.getElementById('closeQuoteModal');

    if (quoteModal) {
        quoteButtons.forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                const service = this.getAttribute('data-service') || '';
                const serviceSelect = document.getElementById('modalServiceSelect');
                if (serviceSelect && service) {
                    serviceSelect.value = service;
                }
                quoteModal.classList.add('active');
                document.body.style.overflow = 'hidden';
            });
        });

        if (closeModal) {
            closeModal.addEventListener('click', function () {
                quoteModal.classList.remove('active');
                document.body.style.overflow = 'auto';
            });
        }

        quoteModal.addEventListener('click', function (e) {
            if (e.target === quoteModal) {
                quoteModal.classList.remove('active');
                document.body.style.overflow = 'auto';
            }
        });
    }

    // ==============================================================
    // 7. AUTO-DISMISS FLASH ALERTS
    // ==============================================================
    const alerts = document.querySelectorAll('.flash-alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });

    // ==============================================================
    // 8. PROJECT GALLERY FILTERING (projects.php)
    // ==============================================================
    const filterBtns = document.querySelectorAll('.filter-btn');
    const projectCards = document.querySelectorAll('.project-item');

    if (filterBtns.length > 0 && projectCards.length > 0) {
        filterBtns.forEach(btn => {
            btn.addEventListener('click', function () {
                filterBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                const filter = this.getAttribute('data-filter');

                projectCards.forEach(card => {
                    if (filter === 'all' || card.getAttribute('data-category') === filter) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
    }

    // ==============================================================
    // 9. FAQ ACCORDION HANDLERS
    // ==============================================================
    const faqItems = document.querySelectorAll('.faq-item');
    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question');
        if (question) {
            question.addEventListener('click', function () {
                const isActive = item.classList.contains('active');
                
                // Close other items
                faqItems.forEach(other => other.classList.remove('active'));

                // Toggle current
                if (!isActive) {
                    item.classList.add('active');
                }
            });
        }
    });

    // ==============================================================
    // 10. TESTIMONIAL AUTO-SWAP SLIDER (index.php & about.php)
    // ==============================================================
    const testimonialSliders = document.querySelectorAll('.testimonial-swap-container');

    testimonialSliders.forEach(container => {
        const slides = container.querySelectorAll('.testimonial-slide-item');
        const dots = container.querySelectorAll('.test-dot');
        const prevBtn = container.querySelector('.test-prev-btn');
        const nextBtn = container.querySelector('.test-next-btn');

        if (slides.length === 0) return;

        let currentIndex = 0;
        const total = slides.length;
        const intervalTime = parseInt(container.getAttribute('data-interval') || '5000', 10);
        let timer = null;

        function setSlide(idx) {
            if (idx >= total) currentIndex = 0;
            else if (idx < 0) currentIndex = total - 1;
            else currentIndex = idx;

            slides.forEach((slide, i) => {
                if (i === currentIndex) {
                    slide.classList.add('active');
                } else {
                    slide.classList.remove('active');
                }
            });

            dots.forEach((dot, i) => {
                if (i === currentIndex) {
                    dot.classList.add('active');
                } else {
                    dot.classList.remove('active');
                }
            });
        }

        function next() {
            setSlide(currentIndex + 1);
        }

        function prev() {
            setSlide(currentIndex - 1);
        }

        function startTimer() {
            stopTimer();
            timer = setInterval(next, intervalTime);
        }

        function stopTimer() {
            if (timer) {
                clearInterval(timer);
                timer = null;
            }
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', function () {
                next();
                startTimer();
            });
        }

        if (prevBtn) {
            prevBtn.addEventListener('click', function () {
                prev();
                startTimer();
            });
        }

        dots.forEach(dot => {
            dot.addEventListener('click', function () {
                const targetIdx = parseInt(this.getAttribute('data-index') || '0', 10);
                setSlide(targetIdx);
                startTimer();
            });
        });

        // Pause rotation on hover
        container.addEventListener('mouseenter', stopTimer);
        container.addEventListener('mouseleave', startTimer);

        // Touch swipe support for mobile
        let touchStartX = 0;
        let touchEndX = 0;

        container.addEventListener('touchstart', function (e) {
            touchStartX = e.changedTouches[0].screenX;
            stopTimer();
        }, { passive: true });

        container.addEventListener('touchend', function (e) {
            touchEndX = e.changedTouches[0].screenX;
            const diff = touchEndX - touchStartX;
            if (Math.abs(diff) > 40) {
                if (diff < 0) {
                    next(); // Swipe left -> next
                } else {
                    prev(); // Swipe right -> prev
                }
            }
            startTimer();
        }, { passive: true });

        // Initialize first slide and start auto-timer
        setSlide(0);
        startTimer();
    });
});
