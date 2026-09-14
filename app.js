/**
 * RUNIT ONEVIA 2026 - Master Presentation & Event Site Engine
 * 34 Zero-Scroll Keynote Slides with Big High-Resolution Visual Assets
 */

document.addEventListener('DOMContentLoaded', () => {
  if (window.lucide) {
    lucide.createIcons();
  }

  // State Variables
  let currentSlideIndex = 0;
  const totalSlides = 39;
  let targetDate = new Date(EVENT_DATA.meta.targetDate);
  let timerInterval = null;
  let presenterTimerInterval = null;
  let elapsedSeconds = 0;
  let currentViewMode = 'deck';
  let eventUnlocked = false;

  // ============================================================
  // 1. PARTICLE CANVAS ANIMATION
  // ============================================================
  const canvas = document.getElementById('particle-canvas');
  const ctx = canvas.getContext('2d');
  let particles = [];

  function resizeCanvas() {
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
  }
  window.addEventListener('resize', resizeCanvas);
  resizeCanvas();

  class Particle {
    constructor() {
      this.x = Math.random() * canvas.width;
      this.y = Math.random() * canvas.height;
      this.vx = (Math.random() - 0.5) * 0.6;
      this.vy = (Math.random() - 0.5) * 0.6;
      this.radius = Math.random() * 2 + 1;
      this.color = Math.random() > 0.5 ? '#00F2FE' : '#7F00FF';
      this.alpha = Math.random() * 0.5 + 0.2;
    }

    update() {
      this.x += this.vx;
      this.y += this.vy;
      if (this.x < 0 || this.x > canvas.width) this.vx *= -1;
      if (this.y < 0 || this.y > canvas.height) this.vy *= -1;
    }

    draw() {
      ctx.beginPath();
      ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2);
      ctx.fillStyle = this.color;
      ctx.globalAlpha = this.alpha;
      ctx.shadowBlur = 10;
      ctx.shadowColor = this.color;
      ctx.fill();
    }
  }

  function initParticles() {
    particles = [];
    const count = Math.floor((canvas.width * canvas.height) / 18000);
    for (let i = 0; i < count; i++) {
      particles.push(new Particle());
    }
  }
  initParticles();

  function animateParticles() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    particles.forEach(p => {
      p.update();
      p.draw();
    });

    for (let i = 0; i < particles.length; i++) {
      for (let j = i + 1; j < particles.length; j++) {
        const dx = particles[i].x - particles[j].x;
        const dy = particles[i].y - particles[j].y;
        const dist = Math.sqrt(dx * dx + dy * dy);
        if (dist < 120) {
          ctx.beginPath();
          ctx.moveTo(particles[i].x, particles[i].y);
          ctx.lineTo(particles[j].x, particles[j].y);
          ctx.strokeStyle = '#00F2FE';
          ctx.globalAlpha = (1 - dist / 120) * 0.15;
          ctx.lineWidth = 0.5;
          ctx.stroke();
        }
      }
    }
    requestAnimationFrame(animateParticles);
  }
  animateParticles();

  // ============================================================
  // 2. COUNTDOWN TIMER & UNLOCK LOGIC
  // ============================================================
  const timerDays = document.getElementById('timer-days');
  const timerHours = document.getElementById('timer-hours');
  const timerMinutes = document.getElementById('timer-minutes');
  const timerSeconds = document.getElementById('timer-seconds');
  const currentClockDisplay = document.getElementById('current-clock-display');
  const targetDateLabel = document.getElementById('target-date-label');
  const countdownOverlay = document.getElementById('countdown-overlay');

  function updateClockAndCountdown() {
    const now = new Date();
    currentClockDisplay.textContent = now.toLocaleTimeString('en-US', { hour12: false });
    targetDateLabel.textContent = targetDate.toLocaleString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' });

    const diff = targetDate - now;

    if (diff <= 0) {
      timerDays.textContent = "00";
      timerHours.textContent = "00";
      timerMinutes.textContent = "00";
      timerSeconds.textContent = "00";

      if (!eventUnlocked) {
        startPresentation();
      }
      return;
    }

    const d = Math.floor(diff / (1000 * 60 * 60 * 24));
    const h = Math.floor((diff / (1000 * 60 * 60)) % 24);
    const m = Math.floor((diff / 1000 / 60) % 60);
    const s = Math.floor((diff / 1000) % 60);

    timerDays.textContent = String(d).padStart(2, '0');
    timerHours.textContent = String(h).padStart(2, '0');
    timerMinutes.textContent = String(m).padStart(2, '0');
    timerSeconds.textContent = String(s).padStart(2, '0');
  }

  timerInterval = setInterval(updateClockAndCountdown, 1000);
  updateClockAndCountdown();

  // Manual Unlock Button
  document.getElementById('btn-unlock-event').addEventListener('click', startPresentation);

  // Home Button Handlers
  const btnHomeScreen = document.getElementById('btn-home-screen');
  const btnHomeDeck = document.getElementById('btn-home-deck');

  if (btnHomeScreen) btnHomeScreen.addEventListener('click', returnToHomeScreen);
  if (btnHomeDeck) btnHomeDeck.addEventListener('click', returnToHomeScreen);

  function returnToHomeScreen() {
    eventUnlocked = false;
    countdownOverlay.classList.remove('hidden');
    countdownOverlay.style.display = 'flex';
    countdownOverlay.style.zIndex = '200';
  }

  function startPresentation() {
    eventUnlocked = true;
    countdownOverlay.classList.add('hidden');
    countdownOverlay.style.display = 'none';
    countdownOverlay.style.zIndex = '-1';
    goToSlide(0);
    startPresenterTimer();
  }

  function startPresenterTimer() {
    if (presenterTimerInterval) return;
    presenterTimerInterval = setInterval(() => {
      elapsedSeconds++;
      const hrs = Math.floor(elapsedSeconds / 3600);
      const mins = Math.floor((elapsedSeconds % 3600) / 60);
      const secs = elapsedSeconds % 60;
      document.getElementById('elapsed-time').textContent = 
        `${String(hrs).padStart(2, '0')}:${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
    }, 1000);
  }

  // Header Timer Configuration Modal
  document.getElementById('btn-timer-settings').addEventListener('click', openTimerModal);

  function openTimerModal() {
    const input = document.getElementById('timer-input-datetime');
    const tzOffset = targetDate.getTimezoneOffset() * 60000;
    const localISOTime = (new Date(targetDate.getTime() - tzOffset)).toISOString().slice(0, 16);
    input.value = localISOTime;
    window.openModal('modal-timer');
  }

  // Keybindings & Presentation Modes are initialized below

  // Initialize Clock & Countdowns
  updateClockAndCountdown();
  timerInterval = setInterval(updateClockAndCountdown, 1000);

  // Quick Lock Code Modal Input Listener
  const lockInput = document.getElementById('lock-passcode');
  if (lockInput) {
    lockInput.addEventListener('keyup', (e) => {
      if (e.key === 'Enter') {
        window.unlockEvent();
      }
    });
  }

  // Presenter Timer Settings Save Listener
  const timerInput = document.getElementById('timer-date-input');
  const btnSaveTimer = document.getElementById('btn-save-timer');
  if (btnSaveTimer && timerInput) {
    btnSaveTimer.addEventListener('click', () => {
      const val = timerInput.value;
      if (val) {
        targetDate = new Date(val);
        updateClockAndCountdown();
        window.closeModal('modal-timer');
      }
    });
  }

  // ============================================================
  // 3. SLIDE DECK RENDERER (39 KEYNOTE SLIDES - ZERO SCROLL)
  // ============================================================
  const slideContentSlot = document.getElementById('slide-content-slot');
  const slideSelector = document.getElementById('slide-selector');
  const currentSlideNumLabel = document.getElementById('current-slide-num');
  const totalSlidesNumLabel = document.getElementById('total-slides-num');
  const progressBar = document.getElementById('progress-bar');

  totalSlidesNumLabel.textContent = totalSlides;

  // Dropdown Options (39 zero-scroll slides)
  const slideTitles = [
    "1. RUNIT ONEVIA 2026",
    "2. Welcome Message",
    "3. Event Agenda",
    "4. Why Runit Exists",
    "5. About Runit Infotech & Focus Areas",
    "6. Runit Brand & Partner Ecosystem",
    "7. Our Growth Journey",
    "8. Mission & Guiding Principles",
    "9. Organization Structure & Vision",
    "10. Legal Credentials: Runit Infotech Govt MSME Udyam",
    "11. Legal Credentials: Lavish Dreamers Pvt Ltd ROC & PAN",
    "12. Legal Credentials: Lavish Enterprises ROC & PAN",
    "13. Team Recognition & Excellence Awards",
    "14. Live Production Apps: MoneyMatrix & Orbita",
    "15. Live Applications: ScripVault & Lavish Dreamers",
    "16. Runit Games Suite & Developer Portfolio",
    "17. Personal Finance Excel Dashboard",
    "18. Desktop Trackers & Enterprise SOPs",
    "19. Smart Links Chrome Extension",
    "20. AI Motion Short: im Moon Spotlight",
    "21. AI Motion Short: Thukalgal Showcase",
    "22. AI Motion Shorts: Kavasam, Signout & Valaiyosai",
    "23. AI Wedding Story Simulations (Part 1)",
    "24. AI Wedding Story Simulations (Part 2)",
    "25. Video Editing & Visual Remixes",
    "26. n8n Workflow Automations",
    "27. Digital & Magazine Publishing Works",
    "28. Enterprise Process Automation & MS Access Tools",
    "29. Product Portfolio Overview",
    "30. Orbita Showcase",
    "31. PowerBooks Showcase",
    "32. MoneyMatrix Rebrand",
    "33. ISPARK Platform",
    "34. MIS Reporting Services",
    "35. 5-Year Strategic Product Roadmap",
    "36. Product Strategy Matrix",
    "37. Team & Core Community Roster",
    "38. Quarterly Rhythm & Objectives",
    "39. Thank You & Open Discussion"
  ];

  slideSelector.innerHTML = slideTitles.map((t, i) => `<option value="${i}">${t}</option>`).join('');

  slideSelector.addEventListener('change', (e) => {
    goToSlide(parseInt(e.target.value, 10));
  });

  function goToSlide(index) {
    if (index < 0) index = 0;
    if (index >= totalSlides) index = totalSlides - 1;
    currentSlideIndex = index;
    renderSlide(currentSlideIndex);

    currentSlideNumLabel.textContent = currentSlideIndex + 1;
    slideSelector.value = currentSlideIndex;
    const progressPercent = ((currentSlideIndex + 1) / totalSlides) * 100;
    progressBar.style.width = `${progressPercent}%`;

    if (window.lucide) lucide.createIcons();
  }

  // Global Carousel State Management
  window.carouselState = window.carouselState || {};

  window.changeCarouselIndex = function(carouselId, delta, count) {
    const current = window.carouselState[carouselId] || 0;
    const next = (current + delta + count) % count;
    window.setCarouselIndex(carouselId, next, count);
  };

  window.setCarouselIndex = function(carouselId, index, count) {
    window.carouselState[carouselId] = index;
    const mainImg = document.getElementById(`carousel-${carouselId}-img`);
    const badge = document.getElementById(`carousel-${carouselId}-badge`);
    if (!mainImg) return;

    mainImg.style.opacity = '0.3';
    mainImg.style.transform = 'scale(0.98)';

    setTimeout(() => {
      try {
        const imagesJson = mainImg.getAttribute('data-images') || '[]';
        const images = JSON.parse(imagesJson);
        if (images[index]) {
          mainImg.src = images[index];
        }
      } catch(e) {}
      mainImg.style.opacity = '1';
      mainImg.style.transform = 'scale(1)';
    }, 80);

    if (badge && count) {
      badge.textContent = `${index + 1} / ${count}`;
    }

    for (let i = 0; i < count; i++) {
      const thumb = document.getElementById(`carousel-${carouselId}-thumb-${i}`);
      if (thumb) {
        if (i === index) thumb.classList.add('active');
        else thumb.classList.remove('active');
      }
    }
  };

  function renderImageCarousel(images, carouselId, title, stageHeight = '230px') {
    if (!images || !images.length) return '';
    const initialIndex = window.carouselState[carouselId] || 0;
    const imagesJson = JSON.stringify(images).replace(/"/g, '&quot;');

    return `
      <div class="carousel-container" id="carousel-${carouselId}" style="margin-top: 8px;">
        <div class="carousel-stage" style="position: relative; height: ${stageHeight}; border-radius: var(--radius-md); overflow: hidden; background: rgba(5,8,17,0.85); border: 1px solid var(--border-glass); display: flex; align-items: center; justify-content: center;">
          <button class="carousel-btn carousel-btn-prev" onclick="event.stopPropagation(); changeCarouselIndex('${carouselId}', -1, ${images.length})" title="Previous Image (<)">
            <i data-lucide="chevron-left"></i>
          </button>
          
          <img id="carousel-${carouselId}-img" 
               src="${images[initialIndex]}" 
               data-images="${imagesJson}"
               class="carousel-img" 
               style="max-height: ${stageHeight}; max-width: 100%; object-fit: contain; cursor: pointer; transition: opacity 0.2s ease, transform 0.2s ease;"
               onclick="openImageModal(this.src, '${title} — Image ' + ((window.carouselState['${carouselId}'] || 0) + 1))" 
               alt="${title}">

          <button class="carousel-btn carousel-btn-next" onclick="event.stopPropagation(); changeCarouselIndex('${carouselId}', 1, ${images.length})" title="Next Image (>)">
            <i data-lucide="chevron-right"></i>
          </button>

          <span class="carousel-badge" id="carousel-${carouselId}-badge">${initialIndex + 1} / ${images.length}</span>
        </div>

        <div class="carousel-thumb-bar">
          ${images.map((img, i) => `
            <img src="${img}" 
                 class="carousel-thumb ${i === initialIndex ? 'active' : ''}" 
                 id="carousel-${carouselId}-thumb-${i}" 
                 onclick="event.stopPropagation(); setCarouselIndex('${carouselId}', ${i}, ${images.length})" 
                 alt="Thumbnail ${i + 1}">
          `).join('')}
        </div>
      </div>
    `;
  }

    function renderSlide(index) {
    let html = '';
    const d = EVENT_DATA;

    switch (index) {
      case 0: // Slide 1: Cover Slide
        html = `
          <div class="slide-header">
            <span class="slide-tag">Annual Flagship Keynote</span>
            <span class="slide-number">Slide 1 of ${totalSlides}</span>
          </div>
          <div style="text-align: center; margin: auto 0;">
            <div style="font-family: var(--font-heading); font-size: 1.8rem; font-weight: 900; letter-spacing: 3px; color: var(--cyan); text-transform: uppercase; margin-bottom: 12px; margin-top: 20px;">
              RUNIT INFOTECH
            </div>
            <h1 class="slide-title" style="font-size: 3.5rem;">${d.meta.title}</h1>
            <p style="font-size: 1.5rem; color: var(--cyan); font-weight: 600; margin-bottom: 12px;">${d.meta.tagline}</p>
            <p class="slide-subtitle" style="max-width: 750px; margin: 0 auto 30px auto;">${d.meta.subtagline}</p>
            <div style="display: inline-flex; gap: 24px; background: rgba(255,255,255,0.04); border: 1px solid var(--border-glass); padding: 12px 28px; border-radius: 30px; font-size: 0.9rem; color: var(--text-secondary);">
              <span><strong style="color: #fff;">Presenter:</strong> ${d.meta.presenter} (${d.meta.role})</span>
              <span>•</span>
              <span><strong style="color: #fff;">Format:</strong> ${d.meta.location}</span>
            </div>
          </div>
        `;
        break;

      case 1: // Slide 2: Welcome Message
        html = `
          <div class="slide-header">
            <span class="slide-tag">Welcome Note</span>
            <span class="slide-number">Slide 2 of ${totalSlides}</span>
          </div>
          <div class="grid-2" style="margin-top: 10px;">
            <div class="content-card" style="display: flex; flex-direction: column; justify-content: space-between;">
              <div>
                <h2 class="slide-title" style="margin-bottom: 8px;">${d.welcome.title}</h2>
                <p style="font-size: 1.1rem; color: var(--cyan); margin-bottom: 16px; font-weight: 600;">${d.welcome.subtitle}</p>
                <div style="font-size: 0.95rem; color: var(--text-secondary); line-height: 1.6; display: flex; flex-direction: column; gap: 12px;">
                  ${d.welcome.paragraphs.map(p => `<p>${p}</p>`).join('')}
                </div>
              </div>
              <div style="margin-top: 20px; border-top: 1px solid var(--border-glass); padding-top: 12px; font-size: 0.85rem; color: var(--text-muted);">
                Keynote presented live by Varun Akshay (Founder & Lead Architect)
              </div>
            </div>

            <div class="content-card highlight-card" style="display: flex; flex-direction: column; justify-content: center; text-align: center; background: radial-gradient(circle at center, rgba(0,242,254,0.1) 0%, transparent 70%);">
              <div style="font-size: 3rem; margin-bottom: 12px; color: var(--cyan);">✨</div>
              <blockquote style="font-family: var(--font-heading); font-size: 1.4rem; color: #fff; font-weight: 700; line-height: 1.4; margin-bottom: 16px;">
                "${d.welcome.quote}"
              </blockquote>
              <div style="font-size: 0.9rem; color: var(--cyan); font-weight: 600;">— ${d.welcome.quoteAuthor}</div>
            </div>
          </div>
        `;
        break;

      case 2: // Slide 3: Event Agenda
        html = `
          <div class="slide-header">
            <span class="slide-tag">Executive Summary</span>
            <span class="slide-number">Slide 3 of ${totalSlides}</span>
          </div>
          <h2 class="slide-title">Event Agenda & Talk Tracks</h2>
          <p class="slide-subtitle">Structured roadmap of key presentations, announcements, and vision demos.</p>
          <div class="grid-3" style="margin-top: 10px;">
            ${d.agenda.map((item, idx) => `
              <div class="content-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                  <span class="product-badge" style="position: static;">Track 0${idx + 1}</span>
                  <span style="font-size: 0.8rem; color: var(--cyan); font-weight: bold;">${item.time}</span>
                </div>
                <h3 style="font-family: var(--font-heading); font-size: 1.1rem; color: #fff; margin-bottom: 6px;">${item.title}</h3>
                <p style="font-size: 0.85rem; color: var(--text-secondary); line-height: 1.4;">${item.desc}</p>
              </div>
            `).join('')}
          </div>
        `;
        break;

      case 3: // Slide 4: Why Runit Exists
        html = `
          <div class="slide-header">
            <span class="slide-tag">Foundational Purpose</span>
            <span class="slide-number">Slide 4 of ${totalSlides}</span>
          </div>
          <h2 class="slide-title">${d.whyRunit.title}</h2>
          <p class="slide-subtitle">${d.whyRunit.subtitle}</p>
          
          <div class="grid-2" style="margin-top: 10px;">
            <div class="content-card">
              <h3 style="color: var(--pink); font-family: var(--font-heading); margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="alert-triangle"></i> The Challenges We Solve
              </h3>
              <ul style="list-style: none; display: flex; flex-direction: column; gap: 10px;">
                ${d.whyRunit.challenges.map(c => `
                  <li style="display: flex; gap: 10px; font-size: 0.9rem; color: var(--text-secondary);">
                    <span style="color: var(--pink); flex-shrink: 0;">✕</span> ${c}
                  </li>
                `).join('')}
              </ul>
            </div>

            <div class="content-card" style="border-left: 4px solid var(--emerald);">
              <h3 style="color: var(--emerald); font-family: var(--font-heading); margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="check-circle-2"></i> Our Solution Framework
              </h3>
              <ul style="list-style: none; display: flex; flex-direction: column; gap: 10px;">
                ${d.whyRunit.solutions.map(s => `
                  <li style="display: flex; gap: 10px; font-size: 0.9rem; color: var(--text-secondary);">
                    <span style="color: var(--emerald); flex-shrink: 0;">✓</span> ${s}
                  </li>
                `).join('')}
              </ul>
            </div>
          </div>
        `;
        break;

      case 4: // Slide 5: About Runit Infotech
        html = `
          <div class="slide-header">
            <span class="slide-tag">Company Profile</span>
            <span class="slide-number">Slide 5 of ${totalSlides}</span>
          </div>
          <h2 class="slide-title">About Runit Infotech & Core Capabilities</h2>
          <p class="slide-subtitle">${d.aboutRunit.overview}</p>
          
          <div class="grid-4" style="margin-top: 14px;">
            ${d.focusAreas.map(f => `
              <div class="content-card" style="padding: 16px;">
                <div style="color: var(--cyan); margin-bottom: 8px;">
                  <i data-lucide="${f.icon}"></i>
                </div>
                <h4 style="font-family: var(--font-heading); font-size: 1rem; color: #fff; margin-bottom: 4px;">${f.name}</h4>
                <p style="font-size: 0.8rem; color: var(--text-secondary); line-height: 1.4;">${f.desc}</p>
              </div>
            `).join('')}
          </div>
        `;
        break;

      case 5: // Slide 6: Brand & Partner Ecosystem
        html = `
          <div class="slide-header">
            <span class="slide-tag">Brand & Partner Ecosystem</span>
            <span class="slide-number">Slide 6 of ${totalSlides}</span>
          </div>
          <h2 class="slide-title">✨ Runit Brand & Partner Ecosystem</h2>
          <p class="slide-subtitle">Integrated parent entity, proprietary SaaS products, venture brands, and strategic enterprise partners.</p>
          
          <div class="grid-4" style="margin-top: 16px;">
            ${d.brandLogos.map(b => `
              <div class="content-card" style="display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 18px;">
                <div style="background: #ffffff; width: 100%; height: 60px; border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; padding: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.3); margin-bottom: 10px;">
                  <img src="${b.logo}" style="max-height: 44px; max-width: 100%; object-fit: contain;" alt="${b.name} Logo" onerror="this.src='https://via.placeholder.com/140x50/FFFFFF/10172A?text=${b.name}'">
                </div>
                <div style="font-family: var(--font-heading); font-weight: 700; font-size: 1.05rem; color: #fff;">${b.name}</div>
                <span class="product-badge" style="position: static; margin-top: 6px; font-size: 0.7rem; padding: 2px 8px;">${b.category}</span>
              </div>
            `).join('')}
          </div>
        `;
        break;

      case 6: // Slide 7: Our Growth Journey
        html = `
          <div class="slide-header">
            <span class="slide-tag">Evolution Timeline</span>
            <span class="slide-number">Slide 7 of ${totalSlides}</span>
          </div>
          <h2 class="slide-title">Our Growth & Transformation Journey</h2>
          <p class="slide-subtitle">From individual learning to an integrated multi-product SaaS & AI technology entity.</p>
          
          <div class="grid-3" style="margin-top: 14px;">
            ${d.journey.map(j => `
              <div class="content-card">
                <span class="product-badge" style="position: static; margin-bottom: 8px;">${j.phase}</span>
                <h3 style="font-family: var(--font-heading); font-size: 1.15rem; color: #fff; margin-bottom: 4px;">${j.title}</h3>
                <p style="font-size: 0.85rem; color: var(--text-secondary); line-height: 1.4;">${j.desc}</p>
              </div>
            `).join('')}
          </div>
        `;
        break;

      case 7: // Slide 8: Mission & Vision
        html = `
          <div class="slide-header">
            <span class="slide-tag">Core Principles</span>
            <span class="slide-number">Slide 8 of ${totalSlides}</span>
          </div>
          <h2 class="slide-title">Mission, Vision & Guiding Principles</h2>
          <p class="slide-subtitle">Engineering principles driving long-term software excellence and value creation.</p>
          
          <div class="grid-2" style="margin-top: 14px;">
            <div class="content-card" style="border-left: 4px solid var(--cyan);">
              <h3 style="color: var(--cyan); font-family: var(--font-heading); margin-bottom: 8px;">Strategic Vision</h3>
              <p style="font-size: 0.95rem; color: var(--text-secondary); line-height: 1.6;">
                To establish Runit Infotech as a premier Indian technology engine delivering high-utility SaaS applications, intelligent AI automation tools, and executive MIS solutions that empower enterprises, SMBs, and retail operations.
              </p>
            </div>

            <div class="content-card" style="border-left: 4px solid var(--purple);">
              <h3 style="color: var(--purple); font-family: var(--font-heading); margin-bottom: 8px;">Execution Mission</h3>
              <p style="font-size: 0.95rem; color: var(--text-secondary); line-height: 1.6;">
                Engineered with precision, zero bloat, and rapid iteration. We build high-throughput tools designed for real business impact, intuitive UX, and seamless scalability.
              </p>
            </div>
          </div>
        `;
        break;

      case 8: // Slide 9: Organization Structure & Vision
        html = `
          <div class="slide-header">
            <span class="slide-tag">Organizational Structure</span>
            <span class="slide-number">Slide 9 of ${totalSlides}</span>
          </div>
          <h2 class="slide-title">Organization Structure & Community Vision</h2>
          <p class="slide-subtitle">How Runit Infotech operates with core leadership and multidisciplinary domain experts.</p>
          
          <div class="grid-3" style="margin-top: 14px;">
            <div class="content-card" style="padding: 24px; border-top: 4px solid var(--cyan);">
              <div style="width: 44px; height: 44px; background: rgba(0,242,254,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 12px; color: var(--cyan);">
                <i data-lucide="shield"></i>
              </div>
              <h3 style="font-family: var(--font-heading); font-size: 1.2rem; color: #fff; margin-bottom: 6px;">Core Technical Leadership</h3>
              <p style="font-size: 0.88rem; color: var(--text-secondary); line-height: 1.5;">
                Led by Founder & Lead Architect Varun Akshay, orchestrating end-to-end product architecture, UI/UX design, and database engineering.
              </p>
            </div>

            <div class="content-card" style="padding: 24px; border-top: 4px solid var(--purple);">
              <div style="width: 44px; height: 44px; background: rgba(127,0,255,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 12px; color: var(--purple);">
                <i data-lucide="users"></i>
              </div>
              <h3 style="font-family: var(--font-heading); font-size: 1.2rem; color: #fff; margin-bottom: 6px;">Multidisciplinary Network</h3>
              <p style="font-size: 0.88rem; color: var(--text-secondary); line-height: 1.5;">
                Supported by domain experts across Graphic Design, MIS Reporting, Team Leadership, IT Infrastructure, Retail, and Supply Chain.
              </p>
            </div>

            <div class="content-card" style="padding: 24px; border-top: 4px solid var(--emerald);">
              <div style="width: 44px; height: 44px; background: rgba(0,245,160,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 12px; color: var(--emerald);">
                <i data-lucide="zap"></i>
              </div>
              <h3 style="font-family: var(--font-heading); font-size: 1.2rem; color: #fff; margin-bottom: 6px;">Small Team. Big Vision.</h3>
              <p style="font-size: 0.88rem; color: var(--text-secondary); line-height: 1.5;">
                Operating lean with high-velocity execution, building SaaS tools and AI solutions to solve enterprise & retail challenges.
              </p>
            </div>
          </div>
        `;
        break;

      case 9: // Slide 10: Legal Credentials: Runit Infotech Govt MSME Udyam
        const leg1 = d.newAdditions.legalRegistrations[0];
        html = `
          <div class="slide-header">
            <span class="slide-tag">Legal Compliance</span>
            <span class="slide-number">Slide 10 of ${totalSlides}</span>
          </div>
          <h2 class="slide-title">Legal Credentials: Runit Infotech</h2>
          <p class="slide-subtitle">Official Govt of India MSME Udyam Registration Certificate loaded directly on canvas.</p>
          
          <div class="content-card asset-card" style="padding: 20px; margin-top: 14px;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
              <h3 style="font-family: var(--font-heading); font-size: 1.25rem; color: #fff;">${leg1.title}</h3>
              <button onclick="openPdfModal('${leg1.file}', '${leg1.title}')" class="btn-primary" style="padding: 6px 14px; font-size: 0.8rem;">Fullscreen PDF →</button>
            </div>
            <div style="width: 100%; height: 340px; border-radius: var(--radius-sm); overflow: hidden; border: 1px solid var(--border-glass); background: #fff; margin-top: 6px;">
              <iframe src="${leg1.file}#toolbar=0" style="width: 100%; height: 100%; border: none;"></iframe>
            </div>
          </div>
        `;
        break;

      case 10: // Slide 11: Legal Credentials: Lavish Dreamers Pvt Ltd ROC & PAN
        const leg2 = d.newAdditions.legalRegistrations[1];
        html = `
          <div class="slide-header">
            <span class="slide-tag">Legal Compliance</span>
            <span class="slide-number">Slide 11 of ${totalSlides}</span>
          </div>
          <h2 class="slide-title">Legal Credentials: Lavish Dreamers Pvt Ltd</h2>
          <p class="slide-subtitle">MCA ROC Incorporation Certificate loaded directly on canvas with Govt PAN Card.</p>
          
          <div class="grid-2" style="margin-top: 14px;">
            <div class="content-card asset-card" style="padding: 20px;">
              <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                <h3 style="font-family: var(--font-heading); font-size: 1.2rem; color: #fff;">MCA ROC Certificate of Incorporation</h3>
                <button onclick="openPdfModal('${leg2.file}', '${leg2.title}')" class="btn-primary" style="padding: 4px 10px; font-size: 0.75rem;">Fullscreen PDF →</button>
              </div>
              <div style="width: 100%; height: 300px; border-radius: var(--radius-sm); overflow: hidden; border: 1px solid var(--border-glass); background: #fff; margin-top: 6px;">
                <iframe src="${leg2.file}#toolbar=0" style="width: 100%; height: 100%; border: none;"></iframe>
              </div>
            </div>

            <div class="content-card asset-card" style="padding: 20px;">
              <h3 style="font-family: var(--font-heading); font-size: 1.2rem; color: #fff; margin-bottom: 6px;">Government PAN Card Identification</h3>
              <p style="font-size: 0.88rem; color: var(--text-secondary); line-height: 1.4; margin-bottom: 12px;">
                Official Permanent Account Number (PAN) tax registration record for corporate governance.
              </p>
              <div style="border-radius: var(--radius-sm); overflow: hidden; border: 1px solid var(--cyan); cursor: pointer; margin-top: 8px;" onclick="openImageModal('${leg2.panImg}', 'Lavish Dreamers Govt PAN Card')">
                <img src="${leg2.panImg}" style="width: 100%; height: 200px; object-fit: cover;" alt="Lavish Dreamers PAN Card">
                <div style="background: rgba(0,0,0,0.85); text-align: center; padding: 6px; font-size: 0.8rem; color: var(--cyan); font-weight: 700;">
                  💳 Click to Enlarge Lavish Dreamers Govt PAN Card
                </div>
              </div>
            </div>
          </div>
        `;
        break;

      case 11: // Slide 12: Legal Credentials: Lavish Enterprises ROC & PAN
        const leg3 = d.newAdditions.legalRegistrations[2];
        html = `
          <div class="slide-header">
            <span class="slide-tag">Legal Compliance</span>
            <span class="slide-number">Slide 12 of ${totalSlides}</span>
          </div>
          <h2 class="slide-title">Legal Credentials: Lavish Enterprises</h2>
          <p class="slide-subtitle">Official MCA ROC Incorporation Certificate loaded directly on canvas with Govt PAN Card.</p>
          
          <div class="grid-2" style="margin-top: 14px;">
            <div class="content-card asset-card" style="padding: 20px;">
              <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                <h3 style="font-family: var(--font-heading); font-size: 1.2rem; color: #fff;">MCA ROC Certificate of Incorporation</h3>
                <button onclick="openPdfModal('${leg3.file}', 'Lavish Enterprises ROC Certificate')" class="btn-primary" style="padding: 4px 10px; font-size: 0.75rem;">Fullscreen PDF →</button>
              </div>
              <div style="width: 100%; height: 300px; border-radius: var(--radius-sm); overflow: hidden; border: 1px solid var(--border-glass); background: #fff; margin-top: 6px;">
                <iframe src="${leg3.file}#toolbar=0" style="width: 100%; height: 100%; border: none;"></iframe>
              </div>
            </div>

            <div class="content-card asset-card" style="padding: 20px;">
              <h3 style="font-family: var(--font-heading); font-size: 1.2rem; color: #fff; margin-bottom: 6px;">Government PAN Card Identification</h3>
              <p style="font-size: 0.88rem; color: var(--text-secondary); line-height: 1.4; margin-bottom: 12px;">
                Official Permanent Account Number (PAN) tax registration record for financial governance.
              </p>
              <div style="border-radius: var(--radius-sm); overflow: hidden; border: 1px solid var(--purple); cursor: pointer; margin-top: 8px;" onclick="openImageModal('${leg3.panImg}', 'Lavish Enterprises Govt PAN Card')">
                <img src="${leg3.panImg}" style="width: 100%; height: 200px; object-fit: cover;" alt="Lavish Enterprises PAN Card">
                <div style="background: rgba(0,0,0,0.85); text-align: center; padding: 6px; font-size: 0.8rem; color: var(--purple); font-weight: 700;">
                  💳 Click to Enlarge Lavish Enterprises Govt PAN Card
                </div>
              </div>
            </div>
          </div>
        `;
        break;

      case 12: // Slide 13: Team Recognition & Excellence Awards
        html = `
          <div class="slide-header">
            <span class="slide-tag">Team Excellence</span>
            <span class="slide-number">Slide 13 of ${totalSlides}</span>
          </div>
          <h2 class="slide-title">Team Recognition & Excellence Awards</h2>
          <p class="slide-subtitle">Honoring outstanding achievements across software architecture, AI automation & MIS excellence.</p>
          
          <div class="grid-3" style="margin-top: 14px;">
            ${d.newAdditions.awards.map(a => `
              <div class="content-card asset-card" style="padding: 16px;">
                <div>
                  <span style="font-size: 0.75rem; font-weight: 700; color: var(--gold); background: rgba(255,215,0,0.1); border: 1px solid var(--gold); padding: 2px 10px; border-radius: 12px; display: inline-block; margin-bottom: 6px;">${a.category}</span>
                  <h3 style="font-family: var(--font-heading); font-size: 1.15rem; color: #fff; margin-bottom: 2px;">${a.recipient}</h3>
                  <p style="color: var(--cyan); font-weight: 600; font-size: 0.88rem; margin-bottom: 6px;">${a.title}</p>
                </div>

                <div style="margin-top: 10px; border-radius: var(--radius-sm); overflow: hidden; border: 1px solid var(--border-glass); cursor: pointer;" onclick="openImageModal('${a.image}', '${a.recipient} - ${a.title}')">
                  <img src="${a.image}" alt="${a.title}" class="asset-thumb-lg" style="height: 190px;" onerror="this.src='https://via.placeholder.com/400x220/10172A/00F2FE?text=Award+Certificate'">
                  <div style="background: rgba(0,0,0,0.8); text-align: center; padding: 6px; font-size: 0.78rem; color: var(--cyan); font-weight: 600;">
                    🔍 Click for full-screen certificate view
                  </div>
                </div>
              </div>
            `).join('')}
          </div>
        `;
        break;

      case 13: // Slide 14: Live Production Apps: MoneyMatrix & Orbita
        const p1 = d.newAdditions.liveDeployments.moneyMatrix;
        const p2 = d.newAdditions.liveDeployments.orbita;
        html = `
          <div class="slide-header">
            <span class="slide-tag">Live Deployments</span>
            <span class="slide-number">Slide 14 of ${totalSlides}</span>
          </div>
          <h2 class="slide-title">Live Production Apps: MoneyMatrix & Orbita</h2>
          <p class="slide-subtitle">Active web applications deployed live on production servers.</p>
          
          <div class="grid-2" style="margin-top: 16px;">
            <div class="content-card asset-card" style="padding: 24px;">
              <div>
                <span class="product-badge" style="position: static; display: inline-block; margin-bottom: 8px;">${p1.category}</span>
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                  <div style="background: #ffffff; padding: 6px 14px; border-radius: var(--radius-sm); display: inline-flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(0,0,0,0.3);">
                    <img src="${p1.logo}" style="height: 28px; max-width: 110px; object-fit: contain;" alt="MoneyMatrix Logo">
                  </div>
                  <h3 style="font-family: var(--font-heading); font-size: 1.4rem; color: #fff;">${p1.name}</h3>
                </div>
                <p style="font-size: 0.95rem; color: var(--text-secondary); line-height: 1.5; margin-bottom: 16px;">${p1.desc}</p>
                <div style="display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 20px;">
                  ${p1.tech.map(t => `<span class="tech-tag">${t}</span>`).join('')}
                </div>
              </div>
              <a href="${p1.link}" target="_blank" class="btn-primary" style="padding: 12px 24px; font-size: 0.95rem; text-decoration: none; text-align: center;">
                <i data-lucide="external-link"></i> Launch Live MoneyMatrix App
              </a>
            </div>

            <div class="content-card asset-card" style="padding: 24px;">
              <div>
                <span class="product-badge" style="position: static; display: inline-block; margin-bottom: 8px;">${p2.category}</span>
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                  <div style="background: #ffffff; padding: 6px 14px; border-radius: var(--radius-sm); display: inline-flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(0,0,0,0.3);">
                    <img src="${p2.logo}" style="height: 28px; max-width: 110px; object-fit: contain;" alt="Orbita Logo">
                  </div>
                  <h3 style="font-family: var(--font-heading); font-size: 1.4rem; color: #fff;">${p2.name}</h3>
                </div>
                <p style="font-size: 0.95rem; color: var(--text-secondary); line-height: 1.5; margin-bottom: 16px;">${p2.desc}</p>
                <div style="display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 20px;">
                  ${p2.tech.map(t => `<span class="tech-tag">${t}</span>`).join('')}
                </div>
              </div>
              <a href="${p2.link}" target="_blank" class="btn-primary" style="padding: 12px 24px; font-size: 0.95rem; text-decoration: none; text-align: center;">
                <i data-lucide="external-link"></i> Launch Live Orbita Platform
              </a>
            </div>
          </div>
        `;
        break;

      case 14: // Slide 15: Live Applications: ScripVault & Lavish Dreamers
        const p3 = d.newAdditions.liveDeployments.scripVault;
        const p4 = d.newAdditions.liveDeployments.lavishDreamers;
        html = `
          <div class="slide-header">
            <span class="slide-tag">Live Deployments</span>
            <span class="slide-number">Slide 15 of ${totalSlides}</span>
          </div>
          <h2 class="slide-title">Live Applications: ScripVault & Lavish Dreamers</h2>
          <p class="slide-subtitle">Cloud snippet vault & direct-to-consumer e-commerce brand platform. Click any image to browse lightbox gallery.</p>
          
          <div class="grid-2" style="margin-top: 14px;">
            <div class="content-card asset-card" style="padding: 18px;">
              <div>
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                  <h3 style="font-family: var(--font-heading); font-size: 1.25rem; color: #fff;">${p3.name}</h3>
                  <a href="${p3.link}" target="_blank" class="btn-primary" style="padding: 4px 12px; font-size: 0.78rem; text-decoration: none;">Live Site →</a>
                </div>
                <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 10px;">${p3.desc}</p>
                <div class="asset-thumb-grid">
                  ${p3.screenshots.map((s, i) => `
                    <img src="${s}" class="asset-thumb-lg" style="height: 135px;" alt="${p3.name}" onclick="openImageModal('${s}', '${p3.name}', ${JSON.stringify(p3.screenshots).replace(/"/g, '&quot;')}, ${i})">
                  `).join('')}
                </div>
              </div>
            </div>

            <div class="content-card asset-card" style="padding: 18px;">
              <div>
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                  <div style="display: flex; align-items: center; gap: 8px;">
                    <div style="background: #ffffff; padding: 3px 8px; border-radius: 4px; display: inline-flex; align-items: center;">
                      <img src="${p4.logo}" style="height: 20px; max-width: 80px; object-fit: contain;" alt="Lavish Dreamers Logo">
                    </div>
                    <h3 style="font-family: var(--font-heading); font-size: 1.25rem; color: #fff;">${p4.name}</h3>
                  </div>
                  <a href="${p4.link}" target="_blank" class="btn-primary" style="padding: 4px 12px; font-size: 0.78rem; text-decoration: none;">Live Site →</a>
                </div>
                <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 10px;">${p4.desc}</p>
                <div class="asset-thumb-grid">
                  ${p4.screenshots.map((s, i) => `
                    <img src="${s}" class="asset-thumb-lg" style="height: 135px;" alt="${p4.name}" onclick="openImageModal('${s}', '${p4.name}', ${JSON.stringify(p4.screenshots).replace(/"/g, '&quot;')}, ${i})">
                  `).join('')}
                </div>
              </div>
            </div>
          </div>
        `;
        break;

      case 15: // Slide 16: Runit Games Suite & Developer Portfolio
        const p5 = d.newAdditions.liveDeployments.runitGames;
        const p6 = d.newAdditions.liveDeployments.varunPortfolio;
        html = `
          <div class="slide-header">
            <span class="slide-tag">Web Platforms</span>
            <span class="slide-number">Slide 16 of ${totalSlides}</span>
          </div>
          <h2 class="slide-title">Runit Games Suite & Developer Portfolio</h2>
          <p class="slide-subtitle">Interactive browser gaming suite & personal developer portfolio site.</p>
          
          <div class="grid-2" style="margin-top: 14px;">
            <div class="content-card asset-card" style="padding: 18px;">
              <div>
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                  <h3 style="font-family: var(--font-heading); font-size: 1.25rem; color: #fff;">${p5.name}</h3>
                  <a href="${p5.link}" target="_blank" class="btn-primary" style="padding: 4px 12px; font-size: 0.78rem; text-decoration: none;">Open Game Suite →</a>
                </div>
                <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 10px;">${p5.desc}</p>
                <div class="asset-thumb-grid">
                  ${p5.screenshots.map((s, i) => `
                    <img src="${s}" class="asset-thumb-lg" style="height: 135px;" alt="${p5.name}" onclick="openImageModal('${s}', '${p5.name}', ${JSON.stringify(p5.screenshots).replace(/"/g, '&quot;')}, ${i})">
                  `).join('')}
                </div>
              </div>
            </div>

            <div class="content-card asset-card" style="padding: 24px; display: flex; flex-direction: column; justify-content: space-between;">
              <div>
                <span class="product-badge" style="position: static; display: inline-block; margin-bottom: 8px;">${p6.category}</span>
                <h3 style="font-family: var(--font-heading); font-size: 1.4rem; color: #fff; margin-bottom: 6px;">${p6.name}</h3>
                <p style="font-size: 0.95rem; color: var(--text-secondary); line-height: 1.6; margin-bottom: 20px;">${p6.desc}</p>
              </div>
              <a href="${p6.link}" target="_blank" class="btn-primary" style="padding: 12px 24px; font-size: 0.95rem; text-decoration: none; text-align: center;">
                <i data-lucide="external-link"></i> Launch Developer Portfolio Site
              </a>
            </div>
          </div>
        `;
        break;

      case 16: // Slide 17: Personal Finance Excel Dashboard
        const fin = d.newAdditions.toolsAndUtilities.excelDashboard;
        html = `
          <div class="slide-header">
            <span class="slide-tag">Financial Analytics</span>
            <span class="slide-number">Slide 17 of ${totalSlides}</span>
          </div>
          <h2 class="slide-title">Personal Finance Excel Dashboard</h2>
          <p class="slide-subtitle">${fin.desc} Click any screen for full-screen lightbox presentation with left/right arrows.</p>
          
          <div class="asset-thumb-grid" style="grid-template-columns: repeat(5, 1fr); margin-top: 18px;">
            ${fin.screenshots.map((s, i) => `
              <div style="border-radius: var(--radius-md); overflow: hidden; border: 1px solid var(--border-glass); cursor: pointer;" onclick="openImageModal('${s}', 'Personal Finance Dashboard', ${JSON.stringify(fin.screenshots).replace(/"/g, '&quot;')}, ${i})">
                <img src="${s}" class="asset-thumb-lg" style="height: 230px;" alt="Personal Finance View ${i+1}">
                <div style="background: rgba(0,0,0,0.8); text-align: center; padding: 6px; font-size: 0.78rem; color: var(--cyan); font-weight: 600;">
                  Screen ${i+1}
                </div>
              </div>
            `).join('')}
          </div>
        `;
        break;

      case 17: // Slide 18: Desktop Trackers & Enterprise SOPs (WITH FULL PDF VIEW BUTTONS)
        const t2 = d.newAdditions.toolsAndUtilities.desktopTimeTracker;
        const t4 = d.newAdditions.toolsAndUtilities.skyeenet;
        html = `
          <div class="slide-header">
            <span class="slide-tag">Desktop & Enterprise Utilities</span>
            <span class="slide-number">Slide 18 of ${totalSlides}</span>
          </div>
          <h2 class="slide-title">Desktop Trackers & Enterprise SOPs</h2>
          <p class="slide-subtitle">PHP desktop time tracker app & Skyeenet Enterprise SOP PDFs loaded directly on canvas.</p>
          
          <div class="grid-2" style="margin-top: 14px;">
            <div class="content-card asset-card" style="padding: 20px;">
              <div>
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                  <div>
                    <span class="product-badge" style="position: static; margin-bottom: 4px;">${t2.category}</span>
                    <h3 style="font-family: var(--font-heading); font-size: 1.2rem; color: #fff;">${t2.name}</h3>
                  </div>
                  <button onclick="openPdfModal('${t2.file}', '${t2.name}')" class="btn-primary" style="padding: 4px 12px; font-size: 0.75rem;">Fullscreen PDF →</button>
                </div>
                <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 8px;">${t2.desc}</p>
              </div>
              <div style="width: 100%; height: 260px; border-radius: var(--radius-sm); overflow: hidden; border: 1px solid var(--border-glass); background: #fff; margin-top: 6px;">
                <iframe src="${t2.file}#toolbar=0" style="width: 100%; height: 100%; border: none;"></iframe>
              </div>
            </div>

            <div class="content-card asset-card" style="padding: 20px;">
              <div>
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                  <div>
                    <span class="product-badge" style="position: static; margin-bottom: 4px;">${t4.category}</span>
                    <h3 style="font-family: var(--font-heading); font-size: 1.2rem; color: #fff;">${t4.name}</h3>
                  </div>
                  <button onclick="openPdfModal('${t4.file}', '${t4.name}')" class="btn-primary" style="padding: 4px 12px; font-size: 0.75rem;">Fullscreen PDF →</button>
                </div>
                <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 8px;">${t4.desc}</p>
              </div>
              <div style="width: 100%; height: 260px; border-radius: var(--radius-sm); overflow: hidden; border: 1px solid var(--border-glass); background: #fff; margin-top: 6px;">
                <iframe src="${t4.file}#toolbar=0" style="width: 100%; height: 100%; border: none;"></iframe>
              </div>
            </div>
          </div>
        `;
        break;

      case 18: // Slide 19: Smart Links Chrome Extension
        const t3 = d.newAdditions.toolsAndUtilities.smartLinks;
        html = `
          <div class="slide-header">
            <span class="slide-tag">Browser Extension</span>
            <span class="slide-number">Slide 19 of ${totalSlides}</span>
          </div>
          <h2 class="slide-title">Smart Links Chrome Extension</h2>
          <p class="slide-subtitle">Browser extension for quick URL shortening, smart redirection & instant analytics. Click any image to browse in lightbox.</p>
          
          <div class="asset-thumb-grid" style="grid-template-columns: repeat(3, 1fr); margin-top: 18px;">
            ${t3.screenshots.map((s, i) => `
              <div style="border-radius: var(--radius-md); overflow: hidden; border: 1px solid var(--border-glass); cursor: pointer;" onclick="openImageModal('${s}', 'Smart Links Chrome Extension View ${i+1}', ${JSON.stringify(t3.screenshots).replace(/"/g, '&quot;')}, ${i})">
                <img src="${s}" class="asset-thumb-lg" style="height: 250px; object-fit: contain; background: rgba(0,0,0,0.5);" alt="Smart Links View ${i+1}">
                <div style="background: rgba(0,0,0,0.85); text-align: center; padding: 6px; font-size: 0.8rem; color: var(--cyan); font-weight: 600;">
                  Extension Screen ${i+1}
                </div>
              </div>
            `).join('')}
          </div>
        `;
        break;

      case 19: // Slide 20: AI Motion Short: "im Moon"
        const moonImages = d.newAdditions.aiCreativeMedia.imMoon;
        html = `
          <div class="slide-header">
            <span class="slide-tag">Generative AI Media</span>
            <span class="slide-number">Slide 20 of ${totalSlides}</span>
          </div>
          <h2 class="slide-title">AI Motion Short: "im Moon"</h2>
          <p class="slide-subtitle">Cinematic generative AI visual short. 5 high-resolution key visual frames. Click any frame to browse in lightbox.</p>
          
          <div class="asset-thumb-grid" style="grid-template-columns: repeat(5, 1fr); margin-top: 18px;">
            ${moonImages.map((img, i) => `
              <div style="border-radius: var(--radius-md); overflow: hidden; border: 1px solid var(--border-glass); cursor: pointer;" onclick="openImageModal('${img}', 'im Moon — Visual Short', ${JSON.stringify(moonImages).replace(/"/g, '&quot;')}, ${i})">
                <img src="${img}" class="asset-thumb-lg" style="height: 230px;" alt="im Moon Frame ${i+1}">
                <div style="background: rgba(0,0,0,0.8); text-align: center; padding: 6px; font-size: 0.78rem; color: var(--cyan); font-weight: 600;">
                  Frame ${i+1}
                </div>
              </div>
            `).join('')}
          </div>
        `;
        break;

      case 20: // Slide 21: AI Motion Short: "Thukalgal"
        const thukalgalImages = d.newAdditions.aiCreativeMedia.thukalgal;
        html = `
          <div class="slide-header">
            <span class="slide-tag">Generative AI Media</span>
            <span class="slide-number">Slide 21 of ${totalSlides}</span>
          </div>
          <h2 class="slide-title">AI Motion Short: "Thukalgal"</h2>
          <p class="slide-subtitle">Atmospheric generative AI visual short. 5 large cinematic key frames. Click any frame to browse in lightbox.</p>
          
          <div class="asset-thumb-grid" style="grid-template-columns: repeat(5, 1fr); margin-top: 18px;">
            ${thukalgalImages.map((img, i) => `
              <div style="border-radius: var(--radius-md); overflow: hidden; border: 1px solid var(--border-glass); cursor: pointer;" onclick="openImageModal('${img}', 'Thukalgal — Visual Short', ${JSON.stringify(thukalgalImages).replace(/"/g, '&quot;')}, ${i})">
                <img src="${img}" class="asset-thumb-lg" style="height: 230px;" alt="Thukalgal Frame ${i+1}">
                <div style="background: rgba(0,0,0,0.8); text-align: center; padding: 6px; font-size: 0.78rem; color: var(--cyan); font-weight: 600;">
                  Frame ${i+1}
                </div>
              </div>
            `).join('')}
          </div>
        `;
        break;

      case 21: // Slide 22: AI Motion Shorts: Kavasam, Signout & Valaiyosai
        const kavasam = d.newAdditions.aiCreativeMedia.kavasam;
        const signout = d.newAdditions.aiCreativeMedia.signout;
        const valaiyosai = d.newAdditions.aiCreativeMedia.valaiyosai;
        html = `
          <div class="slide-header">
            <span class="slide-tag">Generative AI Visual Shorts</span>
            <span class="slide-number">Slide 22 of ${totalSlides}</span>
          </div>
          <h2 class="slide-title">AI Motion Shorts: Kavasam, Signout & Valaiyosai</h2>
          <p class="slide-subtitle">High-resolution cinematic visual posters & keyframes. Click any poster to open lightbox gallery.</p>
          
          <div class="grid-3" style="margin-top: 14px;">
            <div class="content-card asset-card">
              <h4 style="font-family: var(--font-heading); font-size: 1.15rem; color: var(--cyan); margin-bottom: 8px;">Kavasam</h4>
              <div class="asset-thumb-grid" style="grid-template-columns: repeat(3, 1fr);">
                ${kavasam.map((img, i) => `
                  <img src="${img}" class="asset-thumb-lg" style="height: 180px;" alt="Kavasam ${i+1}" onclick="openImageModal('${img}', 'Kavasam Short', ${JSON.stringify(kavasam).replace(/"/g, '&quot;')}, ${i})">
                `).join('')}
              </div>
            </div>

            <div class="content-card asset-card">
              <h4 style="font-family: var(--font-heading); font-size: 1.15rem; color: var(--purple); margin-bottom: 8px;">Signout</h4>
              <div class="asset-thumb-grid" style="grid-template-columns: repeat(3, 1fr);">
                ${signout.map((img, i) => `
                  <img src="${img}" class="asset-thumb-lg" style="height: 180px;" alt="Signout ${i+1}" onclick="openImageModal('${img}', 'Signout Short', ${JSON.stringify(signout).replace(/"/g, '&quot;')}, ${i})">
                `).join('')}
              </div>
            </div>

            <div class="content-card asset-card">
              <h4 style="font-family: var(--font-heading); font-size: 1.15rem; color: var(--emerald); margin-bottom: 8px;">Valaiyosai</h4>
              <div class="asset-thumb-grid" style="grid-template-columns: repeat(3, 1fr);">
                ${valaiyosai.map((img, i) => `
                  <img src="${img}" class="asset-thumb-lg" style="height: 180px;" alt="Valaiyosai ${i+1}" onclick="openImageModal('${img}', 'Valaiyosai Short', ${JSON.stringify(valaiyosai).replace(/"/g, '&quot;')}, ${i})">
                `).join('')}
              </div>
            </div>
          </div>
        `;
        break;

      case 22: // Slide 23: AI Wedding Story Simulations (Part 1)
        const wed1 = d.newAdditions.aiCreativeMedia.weddingSimulationsPart1;
        html = `
          <div class="slide-header">
            <span class="slide-tag">AI Character Consistency</span>
            <span class="slide-number">Slide 23 of ${totalSlides}</span>
          </div>
          <h2 class="slide-title">AI Wedding Story Simulations (Part 1)</h2>
          <p class="slide-subtitle">Photorealistic AI character consistency & storytelling simulations. 3 Large side-by-side high-res frames.</p>
          
          <div class="grid-3" style="margin-top: 18px;">
            ${wed1.map((img, i) => `
              <div style="border-radius: var(--radius-md); overflow: hidden; border: 1px solid var(--border-glass); cursor: pointer;" onclick="openImageModal('${img}', 'AI Wedding Story Simulation (Part 1)', ${JSON.stringify(wed1).replace(/"/g, '&quot;')}, ${i})">
                <img src="${img}" class="asset-thumb-lg" style="height: 250px;" alt="AI Wedding Simulation Frame ${i+1}">
                <div style="background: rgba(0,0,0,0.8); text-align: center; padding: 8px; font-size: 0.85rem; color: var(--cyan); font-weight: 600;">
                  Simulation Frame ${i+1}
                </div>
              </div>
            `).join('')}
          </div>
        `;
        break;

      case 23: // Slide 24: AI Wedding Story Simulations (Part 2)
        const wed2 = d.newAdditions.aiCreativeMedia.weddingSimulationsPart2;
        html = `
          <div class="slide-header">
            <span class="slide-tag">AI Character Consistency</span>
            <span class="slide-number">Slide 24 of ${totalSlides}</span>
          </div>
          <h2 class="slide-title">AI Wedding Story Simulations (Part 2)</h2>
          <p class="slide-subtitle">Photorealistic AI character consistency & storytelling simulations. 3 Large side-by-side high-res frames.</p>
          
          <div class="grid-3" style="margin-top: 18px;">
            ${wed2.map((img, i) => `
              <div style="border-radius: var(--radius-md); overflow: hidden; border: 1px solid var(--border-glass); cursor: pointer;" onclick="openImageModal('${img}', 'AI Wedding Story Simulation (Part 2)', ${JSON.stringify(wed2).replace(/"/g, '&quot;')}, ${i})">
                <img src="${img}" class="asset-thumb-lg" style="height: 250px;" alt="AI Wedding Simulation Frame ${i+4}">
                <div style="background: rgba(0,0,0,0.8); text-align: center; padding: 8px; font-size: 0.85rem; color: var(--cyan); font-weight: 600;">
                  Simulation Frame ${i+4}
                </div>
              </div>
            `).join('')}
          </div>
        `;
        break;

      case 24: // Slide 25: Video Editing & Visual Remixes
        const remixList = d.newAdditions.aiCreativeMedia.remixes;
        html = `
          <div class="slide-header">
            <span class="slide-tag">Creative Media</span>
            <span class="slide-number">Slide 25 of ${totalSlides}</span>
          </div>
          <h2 class="slide-title">Video Editing & Visual Remixes</h2>
          <p class="slide-subtitle">Creative video editing, composite keying, and audio-visual remixes.</p>
          
          <div class="grid-3" style="margin-top: 18px;">
            ${remixList.map((img, i) => `
              <div style="border-radius: var(--radius-md); overflow: hidden; border: 1px solid var(--border-glass); cursor: pointer;" onclick="openImageModal('${img}', 'Video Remixes', ${JSON.stringify(remixList).replace(/"/g, '&quot;')}, ${i})">
                <img src="${img}" class="asset-thumb-lg" style="height: 250px;" alt="Video Remix Frame ${i+1}">
                <div style="background: rgba(0,0,0,0.8); text-align: center; padding: 8px; font-size: 0.85rem; color: var(--cyan); font-weight: 600;">
                  Remix Frame ${i+1}
                </div>
              </div>
            `).join('')}
          </div>
        `;
        break;

      case 25: // Slide 26: n8n Workflow Automations
        const n8nImgs = d.newAdditions.aiCreativeMedia.n8nScreenshots;
        html = `
          <div class="slide-header">
            <span class="slide-tag">Process Automation</span>
            <span class="slide-number">Slide 26 of ${totalSlides}</span>
          </div>
          <h2 class="slide-title">n8n Workflow Automations</h2>
          <p class="slide-subtitle">Zero-touch webhook pipelines, automated email triggers, database sync & multi-service orchestrations. Click any workflow to open lightbox.</p>
          
          <div class="asset-thumb-grid" style="grid-template-columns: repeat(3, 1fr); margin-top: 18px;">
            ${n8nImgs.map((img, i) => `
              <div style="border-radius: var(--radius-md); overflow: hidden; border: 1px solid var(--border-glass); cursor: pointer;" onclick="openImageModal('${img}', 'n8n Workflow Automation ${i+1}', ${JSON.stringify(n8nImgs).replace(/"/g, '&quot;')}, ${i})">
                <img src="${img}" class="asset-thumb-lg" style="height: 250px; object-fit: cover;" alt="n8n Workflow ${i+1}">
                <div style="background: rgba(0,0,0,0.85); text-align: center; padding: 6px; font-size: 0.8rem; color: var(--cyan); font-weight: 600;">
                  n8n Workflow ${i+1}
                </div>
              </div>
            `).join('')}
          </div>
        `;
        break;

      case 26: // Slide 27: Digital & Magazine Publishing Works
        const magImgs = d.newAdditions.aiCreativeMedia.magazineWorks;
        html = `
          <div class="slide-header">
            <span class="slide-tag">Corporate Publishing</span>
            <span class="slide-number">Slide 27 of ${totalSlides}</span>
          </div>
          <h2 class="slide-title">Digital & Magazine Publishing Works</h2>
          <p class="slide-subtitle">Publication-grade magazine typography, ignition system engineering covers & digital media releases. Click any issue to browse lightbox.</p>
          
          <div class="asset-thumb-grid" style="grid-template-columns: repeat(2, 1fr); margin-top: 18px;">
            ${magImgs.map((img, i) => `
              <div style="border-radius: var(--radius-md); overflow: hidden; border: 1px solid var(--border-glass); cursor: pointer;" onclick="openImageModal('${img}', 'Magazine Publishing Issue ${i+1}', ${JSON.stringify(magImgs).replace(/"/g, '&quot;')}, ${i})">
                <img src="${img}" class="asset-thumb-lg" style="height: 260px; object-fit: contain; background: rgba(0,0,0,0.5);" alt="Magazine Issue ${i+1}">
                <div style="background: rgba(0,0,0,0.85); text-align: center; padding: 8px; font-size: 0.85rem; color: var(--cyan); font-weight: 600;">
                  Publication Cover / Page ${i+1}
                </div>
              </div>
            `).join('')}
          </div>
        `;
        break;

      case 27: // Slide 28: Enterprise Process Automation & MS Access Tools
        html = `
          <div class="slide-header">
            <span class="slide-tag">Enterprise Operations</span>
            <span class="slide-number">Slide 28 of ${totalSlides}</span>
          </div>
          <h2 class="slide-title">Enterprise Process Automation & MS Access Tools</h2>
          <p class="slide-subtitle">n8n zero-touch workflow engines, MS Access operational database tools & publication-grade document design.</p>
          
          <div class="grid-3" style="margin-top: 16px;">
            ${d.newAdditions.automationAndPublishing.map(item => `
              <div class="content-card" style="padding: 24px;">
                <div style="width: 44px; height: 44px; background: rgba(0,242,254,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 12px; color: var(--cyan);">
                  <i data-lucide="${item.icon}"></i>
                </div>
                <h3 style="font-family: var(--font-heading); font-size: 1.2rem; color: #fff; margin-bottom: 6px;">${item.title}</h3>
                <p style="font-size: 0.88rem; color: var(--text-secondary); line-height: 1.5;">${item.desc}</p>
              </div>
            `).join('')}
          </div>
        `;
        break;

      case 28: // Slide 29: Product Portfolio Overview
        html = `
          <div class="slide-header">
            <span class="slide-tag">Product Ecosystem</span>
            <span class="slide-number">Slide 29 of ${totalSlides}</span>
          </div>
          <h2 class="slide-title">Product Portfolio Overview</h2>
          <p class="slide-subtitle">5 Core Pillars powering business productivity, financial tracking, learning, and MIS intelligence.</p>
          <div class="grid-3" style="margin-top: 10px;">
            ${d.products.map(p => `
              <div class="content-card" style="cursor: pointer;" onclick="openProductModal('${p.id}')">
                <span class="product-badge" style="position: static; display: inline-block; margin-bottom: 8px;">${p.badge}</span>
                <h3 style="font-family: var(--font-heading); font-size: 1.25rem; font-weight: 700; color: #fff;">${p.name}</h3>
                <p style="font-size: 0.8rem; color: var(--cyan); margin-bottom: 8px;">${p.category}</p>
                <p style="font-size: 0.85rem; color: var(--text-secondary);">${p.tagline}</p>
                <div style="margin-top: 12px; font-size: 0.75rem; color: var(--emerald); font-weight: 600;">
                  Click to view showcase details →
                </div>
              </div>
            `).join('')}
          </div>
        `;
        break;

      case 29: // Slide 30: Orbita Showcase
        html = renderProductSlide(d.products[0], 30);
        break;

      case 30: // Slide 31: PowerBooks Showcase
        html = renderProductSlide(d.products[1], 31);
        break;

      case 31: // Slide 32: MoneyMatrix Spotlight
        html = `
          <div class="slide-header">
            <span class="slide-tag">Product Rebrand & Spotlight</span>
            <span class="slide-number">Slide 32 of ${totalSlides}</span>
          </div>
          <div class="product-spotlight">
            <div class="product-info">
              <div>
                <span class="product-badge" style="position: static; background: var(--pink); border-color: var(--pink); color: #fff;">${d.products[2].badge}</span>
                <span style="font-size: 0.85rem; color: var(--gold); margin-left: 10px; font-weight: 600;">${d.products[2].rebrandNote}</span>
              </div>
              <div style="display: flex; align-items: center; gap: 14px; margin-top: 8px; margin-bottom: 6px;">
                <div style="background: #ffffff; padding: 6px 14px; border-radius: var(--radius-sm); display: inline-flex; align-items: center; justify-content: center; box-shadow: 0 4px 14px rgba(0,0,0,0.3);">
                  <img src="${d.products[2].image}" style="max-height: 36px; max-width: 140px; object-fit: contain;" alt="MoneyMatrix Logo">
                </div>
                <h2 class="slide-title" style="margin: 0;">${d.products[2].name}</h2>
              </div>
              <p style="color: var(--cyan); font-weight: 600; font-size: 0.95rem;">${d.products[2].tagline}</p>
              <p style="color: var(--text-secondary); font-size: 0.9rem; line-height: 1.5;">${d.products[2].description}</p>
              
              <div style="background: rgba(255,255,255,0.03); border: 1px solid var(--border-glass); padding: 12px; border-radius: var(--radius-sm);">
                <span style="font-size: 0.8rem; font-weight: bold; color: var(--cyan);">Suggested Rebrand Titles:</span>
                <div style="display: flex; gap: 8px; flex-wrap: wrap; margin-top: 6px;">
                  <span class="tech-tag" style="border-color: var(--cyan); color: #fff;">MoneyMatrix</span>
                  <span class="tech-tag">CashPilot</span>
                  <span class="tech-tag">Runit Ledger</span>
                  <span class="tech-tag">MoneyTrail</span>
                  <span class="tech-tag">ExpenseHub</span>
                </div>
              </div>

              <div style="display: flex; gap: 12px;">
                <a href="${d.products[2].link}" target="_blank" class="btn-primary" style="padding: 10px 20px; font-size: 0.9rem;">
                  <i data-lucide="external-link"></i> Launch Demo
                </a>
              </div>
            </div>
            
            <div class="product-media-wrapper">
              <img src="${d.products[2].image}" alt="MoneyMatrix Logo Showcase" class="product-img" style="background: #fff; padding: 20px; object-fit: contain;" onerror="this.src='https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?w=800&auto=format&fit=crop&q=80'">
            </div>
          </div>
        `;
        break;

      case 32: // Slide 33: ISPARK Spotlight
        html = renderProductSlide(d.products[3], 33);
        break;

      case 33: // Slide 34: MIS Services Spotlight
        const mis = d.products[4];
        html = `
          <div class="slide-header">
            <span class="slide-tag">Business Intelligence</span>
            <span class="slide-number">Slide 34 of ${totalSlides}</span>
          </div>
          <h2 class="slide-title">MIS Reporting Services</h2>
          <p class="slide-subtitle">${mis.description} Click any dashboard screen for full-screen lightbox presentation.</p>

          <div class="grid-2" style="margin-top: 14px;">
            <div class="content-card" style="padding: 20px; display: flex; flex-direction: column; justify-content: space-between;">
              <div>
                <span class="product-badge" style="position: static; margin-bottom: 6px;">${mis.badge}</span>
                <h3 style="font-family: var(--font-heading); font-size: 1.3rem; color: var(--cyan); margin-bottom: 6px;">${mis.tagline}</h3>
                <p style="font-size: 0.9rem; color: var(--text-secondary); line-height: 1.5; margin-bottom: 12px;">${mis.description}</p>
                
                <h4 style="font-family: var(--font-heading); font-size: 1rem; color: #fff; margin-bottom: 8px;">Key Capabilities:</h4>
                <ul style="list-style: none; display: flex; flex-direction: column; gap: 6px; margin-bottom: 16px;">
                  ${mis.features.map(f => `
                    <li style="display: flex; gap: 8px; font-size: 0.85rem; color: var(--text-secondary);">
                      <span style="color: var(--emerald);">✓</span> ${f}
                    </li>
                  `).join('')}
                </ul>
              </div>

              <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                ${mis.tech.map(t => `<span class="tech-tag">${t}</span>`).join('')}
              </div>
            </div>

            <div class="content-card asset-card" style="padding: 16px;">
              <h4 style="font-family: var(--font-heading); font-size: 1.1rem; color: #fff; margin-bottom: 8px;">Executive MIS Dashboards (5 Screenshots)</h4>
              <div class="asset-thumb-grid" style="grid-template-columns: repeat(3, 1fr); gap: 10px;">
                ${mis.screenshots.map((img, i) => `
                  <div style="border-radius: var(--radius-sm); overflow: hidden; border: 1px solid var(--border-glass); cursor: pointer;" onclick="openImageModal('${img}', 'MIS Reporting Dashboard View ${i+1}', ${JSON.stringify(mis.screenshots).replace(/"/g, '&quot;')}, ${i})">
                    <img src="${img}" class="asset-thumb-lg" style="height: 110px; object-fit: cover;" alt="MIS Report ${i+1}">
                    <div style="background: rgba(0,0,0,0.85); text-align: center; padding: 4px; font-size: 0.75rem; color: var(--cyan); font-weight: 600;">
                      View ${i+1}
                    </div>
                  </div>
                `).join('')}
              </div>
            </div>
          </div>
        `;
        break;

      case 34: // Slide 35: 5-Year Roadmap
        html = `
          <div class="slide-header">
            <span class="slide-tag">Strategic Vision</span>
            <span class="slide-number">Slide 35 of ${totalSlides}</span>
          </div>
          <h2 class="slide-title">5-Year Strategic Product & Execution Roadmap</h2>
          <p class="slide-subtitle">Phased execution matrix from 2026 foundation through 2030+ industry leadership.</p>
          <div class="timeline-matrix" style="margin-top: 10px;">
            ${d.roadmap.map(r => `
              <div class="timeline-phase">
                <span class="timeline-years">${r.years}</span>
                <h4 style="font-family: var(--font-heading); color: #fff; margin: 4px 0 8px 0; font-size: 1rem;">${r.title}</h4>
                <ul class="timeline-list">
                  ${r.items.map(item => `<li>${item}</li>`).join('')}
                </ul>
              </div>
            `).join('')}
          </div>
        `;
        break;

      case 35: // Slide 36: Product Strategy Matrix
        html = `
          <div class="slide-header">
            <span class="slide-tag">Market Strategy</span>
            <span class="slide-number">Slide 36 of ${totalSlides}</span>
          </div>
          <h2 class="slide-title">Product Strategy Matrix</h2>
          <p class="slide-subtitle">Targeted solutions engineered for enterprise MIS, retail analytics, and BFSI operations.</p>
          <div class="grid-3" style="margin-top: 10px;">
            <div class="content-card">
              <h4 style="color: var(--cyan); font-family: var(--font-heading); margin-bottom: 6px;">Business Productivity</h4>
              <p style="font-size: 0.85rem; color: var(--text-secondary);">Orbita, PowerBooks, MoneyMatrix expense management.</p>
            </div>
            <div class="content-card">
              <h4 style="color: var(--purple); font-family: var(--font-heading); margin-bottom: 6px;">Business Intelligence</h4>
              <p style="font-size: 0.85rem; color: var(--text-secondary);">MIS Executive Reporting, Productivity Dashboards, Recovery analytics.</p>
            </div>
            <div class="content-card">
              <h4 style="color: var(--emerald); font-family: var(--font-heading); margin-bottom: 6px;">Retail & BFSI MIS</h4>
              <p style="font-size: 0.85rem; color: var(--text-secondary);">Inventory performance, Collection MIS, Vendor billing audit.</p>
            </div>
            <div class="content-card">
              <h4 style="color: var(--gold); font-family: var(--font-heading); margin-bottom: 6px;">AI & Automation Services</h4>
              <p style="font-size: 0.85rem; color: var(--text-secondary);">AI Productivity Consulting, Automated data pipelines, Workflow bots.</p>
            </div>
            <div class="content-card">
              <h4 style="color: var(--pink); font-family: var(--font-heading); margin-bottom: 6px;">Financial Control</h4>
              <p style="font-size: 0.85rem; color: var(--text-secondary);">Cash flow visibility, ledger management, automated expense audit.</p>
            </div>
            <div class="content-card">
              <h4 style="color: var(--blue); font-family: var(--font-heading); margin-bottom: 6px;">Community Learning</h4>
              <p style="font-size: 0.85rem; color: var(--text-secondary);">ISPARK innovation hub, skill exchanges, tech incubation.</p>
            </div>
          </div>
        `;
        break;

      case 36: // Slide 37: Team & Core Community Roster
        html = `
          <div class="slide-header">
            <span class="slide-tag">Member Presentations</span>
            <span class="slide-number">Slide 37 of ${totalSlides}</span>
          </div>
          <h2 class="slide-title" style="margin-bottom: 4px;">Team & Core Community Roster</h2>
          <p class="slide-subtitle" style="margin-bottom: 16px;">Click any team member card to view their profession-tailored presentation content & talk track.</p>
          
          <div style="display: flex; flex-direction: column; gap: 20px; max-height: 420px; overflow-y: auto; padding-right: 8px;">
            <div>
              <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 10px;">
                <span style="width: 10px; height: 10px; background: var(--emerald); border-radius: 50%; display: inline-block;"></span>
                <h3 style="font-family: var(--font-heading); font-size: 1.1rem; color: var(--emerald); font-weight: 700;">In Meeting (Live Speakers)</h3>
              </div>
              <div class="grid-3">
                ${d.teamGroups.inMeeting.map(m => `
                  <div class="team-card" style="cursor: pointer;" onclick="openTeamMemberModal('${m.id}')">
                    <img src="${m.avatar}" alt="${m.name}" class="team-avatar" onerror="this.src='https://via.placeholder.com/50/00F2FE/FFFFFF?text=${m.name.charAt(0)}'">
                    <div>
                      <div class="team-name">${m.name}</div>
                      <div class="team-role" style="color: var(--emerald);">${m.role}</div>
                      <div class="team-company">${m.company}</div>
                    </div>
                  </div>
                `).join('')}
              </div>
            </div>

            <div>
              <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 10px;">
                <span style="width: 10px; height: 10px; background: var(--purple); border-radius: 50%; display: inline-block;"></span>
                <h3 style="font-family: var(--font-heading); font-size: 1.1rem; color: var(--purple); font-weight: 700;">Core Contributors (Non-Participants)</h3>
              </div>
              <div class="grid-3">
                ${d.teamGroups.nonParticipants.map(m => `
                  <div class="team-card" style="cursor: pointer;" onclick="openTeamMemberModal('${m.id}')">
                    <img src="${m.avatar}" alt="${m.name}" class="team-avatar" onerror="this.src='https://via.placeholder.com/50/7F00FF/FFFFFF?text=${m.name.charAt(0)}'">
                    <div>
                      <div class="team-name">${m.name}</div>
                      <div class="team-role" style="color: var(--purple);">${m.role}</div>
                      <div class="team-company">${m.company}</div>
                    </div>
                  </div>
                `).join('')}
              </div>
            </div>
          </div>
        `;
        break;

      case 37: // Slide 38: Quarterly Rhythm & Objectives
        html = `
          <div class="slide-header">
            <span class="slide-tag">Operational Rhythm</span>
            <span class="slide-number">Slide 38 of ${totalSlides}</span>
          </div>
          <h2 class="slide-title">Quarterly Rhythm & Strategic Alignment</h2>
          <p class="slide-subtitle">Runit Onevia Annual Flagship + Runit Nextora Quarterly Sprint Engine.</p>
          
          <div class="grid-2" style="margin-top: 16px;">
            <div class="content-card" style="border-left: 4px solid var(--cyan);">
              <h3 style="color: var(--cyan); font-family: var(--font-heading); margin-bottom: 8px;">Runit Onevia Flagship</h3>
              <p style="font-size: 0.9rem; color: var(--text-secondary); line-height: 1.5;">
                Annual flagship vision event setting our strategic 5-year product direction, company milestones, brand identity, and community expansion.
              </p>
            </div>
            <div class="content-card" style="border-left: 4px solid var(--purple);">
              <h3 style="color: var(--purple); font-family: var(--font-heading); margin-bottom: 8px;">Runit Nextora Cadence</h3>
              <p style="font-size: 0.9rem; color: var(--text-secondary); line-height: 1.5;">
                <strong>Runit Nextora</strong> will serve as our quarterly operational review rhythm to monitor sprint completion, product deployments, feature roadmap progress, and community growth.
              </p>
            </div>
          </div>
        `;
        break;

      case 38: // Slide 39: Thank You & Grand Finale
        html = `
          <div class="slide-header">
            <span class="slide-tag">Grand Finale</span>
            <span class="slide-number">Slide 39 of ${totalSlides}</span>
          </div>
          <div style="text-align: center; margin: auto 0;">
            <div style="display: flex; justify-content: center; gap: 10px; margin-bottom: 12px;">
              <i data-lucide="heart" style="width: 44px; height: 44px; color: var(--pink);"></i>
              <i data-lucide="sparkles" style="width: 44px; height: 44px; color: var(--cyan);"></i>
            </div>
            
            <h2 class="slide-title" style="font-size: 2.8rem; margin-bottom: 6px;">Thank You Team & Open Discussion</h2>
            <p class="slide-subtitle" style="max-width: 750px; margin: 0 auto 20px auto; color: var(--text-secondary);">
              To all core contributors, designers, MIS experts, leads, and entrepreneurs — thank you for your vision and dedication.
            </p>

            <div style="background: rgba(0, 242, 254, 0.05); border: 1px solid var(--border-glow); padding: 16px; border-radius: var(--radius-md); max-width: 680px; margin: 0 auto 24px auto;">
              <h4 style="font-family: var(--font-heading); color: var(--cyan); font-size: 1.1rem; margin-bottom: 4px;">Open Floor Strategic Discussion</h4>
              <p style="font-size: 0.9rem; color: #fff;">Questions • Feedback • Collaboration Opportunities • Strategic Ideas</p>
            </div>

            <div style="background: var(--gradient-cyan-purple); padding: 20px 30px; border-radius: var(--radius-lg); max-width: 800px; margin: 0 auto; box-shadow: 0 10px 40px rgba(0,242,254,0.3);">
              <h3 style="font-family: var(--font-heading); font-weight: 800; font-size: 1.35rem; color: #fff; margin-bottom: 4px;">
                ${d.motto}
              </h3>
              <p style="font-size: 0.85rem; color: rgba(255,255,255,0.85);">
                Runit Infotech — Annual Flagship Vision 2026
              </p>
            </div>
          </div>
        `;
        break;
    }

    slideContentSlot.innerHTML = html;
  }

  function renderProductSlide(p, slideNum) {
    return `
      <div class="slide-header">
        <span class="slide-tag">Product Showcase</span>
        <span class="slide-number">Slide ${slideNum} of ${totalSlides}</span>
      </div>
      <div class="product-spotlight">
        <div class="product-info">
          <div>
            <span class="product-badge" style="position: static;">${p.badge}</span>
            <span style="font-size: 0.85rem; color: var(--cyan); margin-left: 10px; font-weight: 600;">${p.category}</span>
          </div>

          <div style="display: flex; align-items: center; gap: 14px; margin-top: 8px; margin-bottom: 6px;">
            <div style="background: #ffffff; padding: 6px 14px; border-radius: var(--radius-sm); display: inline-flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(0,0,0,0.3);">
              <img src="${p.image}" style="max-height: 36px; max-width: 140px; object-fit: contain;" alt="${p.name} Logo" onerror="this.src='https://via.placeholder.com/120x40/FFFFFF/10172A?text=${p.name}'">
            </div>
            <h2 class="slide-title" style="margin: 0;">${p.name}</h2>
          </div>

          <p style="color: var(--cyan); font-weight: 600; font-size: 0.95rem;">${p.tagline}</p>
          <p style="color: var(--text-secondary); font-size: 0.9rem; line-height: 1.5;">${p.description}</p>
          
          <ul class="product-features-list">
            ${p.features.map(f => `<li>${f}</li>`).join('')}
          </ul>

          <div class="tech-tags">
            ${p.tech.map(t => `<span class="tech-tag">${t}</span>`).join('')}
          </div>

          <div style="display: flex; gap: 12px; margin-top: 8px;">
            <a href="${p.link}" target="_blank" class="btn-primary" style="padding: 10px 20px; font-size: 0.9rem;">
              <i data-lucide="external-link"></i> Launch Demo
            </a>
            <button class="btn-secondary" style="padding: 10px 16px; font-size: 0.85rem;" onclick="openProductModal('${p.id}')">
              Full Overview
            </button>
          </div>
        </div>

        <div class="product-media-wrapper">
          <img src="${p.image}" alt="${p.name} Screenshot" class="product-img" style="background: #ffffff; padding: 24px; object-fit: contain;" onerror="this.src='https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800&auto=format&fit=crop&q=80'">
        </div>
      </div>
    `;
  }

  // ============================================================
  // 4. NAVIGATION CONTROLS (KEYBOARD & MOUSE SCROLL)
  // ============================================================
  document.getElementById('btn-prev-slide').addEventListener('click', () => goToSlide(currentSlideIndex - 1));
  document.getElementById('btn-next-slide').addEventListener('click', () => goToSlide(currentSlideIndex + 1));

  window.addEventListener('keydown', (e) => {
    if (document.querySelector('.modal-backdrop.active')) return;

    if (e.key === 'ArrowRight' || e.key === 'Space' || e.key === 'PageDown') {
      e.preventDefault();
      goToSlide(currentSlideIndex + 1);
    } else if (e.key === 'ArrowLeft' || e.key === 'PageUp') {
      e.preventDefault();
      goToSlide(currentSlideIndex - 1);
    } else if (e.key.toLowerCase() === 'f') {
      toggleFullscreen();
    }
  });

  // Mouse Scroll / Trackpad Navigation
  let isScrolling = false;
  window.addEventListener('wheel', (e) => {
    if (currentViewMode !== 'deck') return;
    if (document.querySelector('.modal-backdrop.active')) return;

    if (isScrolling) return;

    if (e.deltaY > 20) {
      isScrolling = true;
      goToSlide(currentSlideIndex + 1);
      setTimeout(() => { isScrolling = false; }, 400);
    } else if (e.deltaY < -20) {
      isScrolling = true;
      goToSlide(currentSlideIndex - 1);
      setTimeout(() => { isScrolling = false; }, 400);
    }
  }, { passive: true });

  goToSlide(0);

  // ============================================================
  // 5. VIEW MODE SWITCHER
  // ============================================================
  const btnViewDeck = document.getElementById('btn-view-deck');
  const btnViewPortal = document.getElementById('btn-view-portal');
  const deckView = document.getElementById('deck-view');
  const portalView = document.getElementById('portal-view');
  const presenterBar = document.getElementById('presenter-bar');

  btnViewDeck.addEventListener('click', () => switchViewMode('deck'));
  btnViewPortal.addEventListener('click', () => switchViewMode('portal'));

  function switchViewMode(mode) {
    currentViewMode = mode;
    if (mode === 'deck') {
      btnViewDeck.classList.add('active');
      btnViewPortal.classList.remove('active');
      deckView.style.display = 'flex';
      portalView.style.display = 'none';
      presenterBar.style.display = 'flex';
    } else {
      btnViewPortal.classList.add('active');
      btnViewDeck.classList.remove('active');
      deckView.style.display = 'none';
      portalView.style.display = 'flex';
      presenterBar.style.display = 'none';
      renderPortalView();
    }
    if (window.lucide) lucide.createIcons();
  }

  function renderPortalView() {
    let portalHtml = '';
    const d = EVENT_DATA;

    portalHtml += `
      <div class="portal-section">
        <span class="slide-tag">Executive Overview</span>
        <h1 style="font-family: var(--font-heading); font-size: 2.8rem; color: #fff; margin-top: 10px;">${d.meta.title}</h1>
        <p style="font-size: 1.2rem; color: var(--cyan); font-weight: 600;">${d.meta.tagline}</p>
        <p style="color: var(--text-secondary); margin-top: 8px;">${d.meta.subtagline}</p>
      </div>
    `;

    portalHtml += `
      <div class="portal-section">
        <h2 class="portal-section-title">In Meeting Speakers</h2>
        <div class="grid-3" style="margin-top: 20px;">
          ${d.teamGroups.inMeeting.map(m => `
            <div class="team-card" style="cursor: pointer;" onclick="openTeamMemberModal('${m.id}')">
              <img src="${m.avatar}" class="team-avatar" alt="${m.name}">
              <div>
                <div class="team-name">${m.name}</div>
                <div class="team-role" style="color: var(--emerald);">${m.role}</div>
                <div class="team-company">${m.company}</div>
              </div>
            </div>
          `).join('')}
        </div>
      </div>
    `;

    portalHtml += `
      <div class="portal-section">
        <h2 class="portal-section-title">Non Participant Team Members</h2>
        <div class="grid-3" style="margin-top: 20px;">
          ${d.teamGroups.nonParticipants.map(m => `
            <div class="team-card" style="cursor: pointer;" onclick="openTeamMemberModal('${m.id}')">
              <img src="${m.avatar}" class="team-avatar" alt="${m.name}">
              <div>
                <div class="team-name">${m.name}</div>
                <div class="team-role" style="color: var(--purple);">${m.role}</div>
                <div class="team-company">${m.company}</div>
              </div>
            </div>
          `).join('')}
        </div>
      </div>
    `;

    portalView.innerHTML = portalHtml;
  }

  // ============================================================
  // 6. MEDIA MANAGER & LIGHTBOX MODALS
  // ============================================================
  document.getElementById('btn-media-editor').addEventListener('click', openMediaEditorModal);

  function openMediaEditorModal() {
    const form = document.getElementById('media-form');
    form.innerHTML = EVENT_DATA.products.map((p, i) => `
      <div style="background: rgba(255,255,255,0.03); border: 1px solid var(--border-glass); padding: 14px; border-radius: var(--radius-sm);">
        <h4 style="color: var(--cyan); font-family: var(--font-heading); margin-bottom: 10px;">${p.name} (${p.category})</h4>
        <div class="form-group" style="margin-bottom: 10px;">
          <label>Image / Screenshot URL</label>
          <input type="url" id="media-img-${i}" class="form-input" value="${p.image}">
        </div>
        <div class="form-group">
          <label>Live Demo Website Link</label>
          <input type="url" id="media-link-${i}" class="form-input" value="${p.link}">
        </div>
      </div>
    `).join('');
    window.openModal('modal-media');
  }

  document.getElementById('btn-save-media').addEventListener('click', () => {
    EVENT_DATA.products.forEach((p, i) => {
      const imgInput = document.getElementById(`media-img-${i}`);
      const linkInput = document.getElementById(`media-link-${i}`);
      if (imgInput && imgInput.value) p.image = imgInput.value;
      if (linkInput && linkInput.value) p.link = linkInput.value;
    });

    window.closeModal('modal-media');
    if (currentViewMode === 'deck') {
      renderSlide(currentSlideIndex);
    } else {
      renderPortalView();
    }
  });

  // Fullscreen
  document.getElementById('btn-fullscreen').addEventListener('click', toggleFullscreen);

  function toggleFullscreen() {
    if (!document.fullscreenElement) {
      document.documentElement.requestFullscreen().catch(err => console.log(err));
    } else {
      if (document.exitFullscreen) document.exitFullscreen();
    }
  }

  // Global Modal Utilities
  window.openModal = function(id) {
    const modal = document.getElementById(id);
    if (modal) {
      modal.classList.add('active');
      if (window.lucide) lucide.createIcons();
    }
  };

  window.closeModal = function(id) {
    const modal = document.getElementById(id);
    if (modal) modal.classList.remove('active');
  };

  // PDF Document Viewer Modal
  window.openPdfModal = function(pdfUrl, title) {
    const body = document.getElementById('modal-product-body');
    body.innerHTML = `
      <h3 style="font-family: var(--font-heading); color: var(--cyan); font-size: 1.3rem; margin-bottom: 8px;">📄 ${title}</h3>
      <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 12px;">Viewing PDF Document in embedded viewer.</p>
      
      <div style="width: 100%; height: 500px; border-radius: var(--radius-md); overflow: hidden; border: 1px solid var(--border-glass); background: #fff;">
        <iframe src="${pdfUrl}" style="width: 100%; height: 100%; border: none;"></iframe>
      </div>

      <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 16px;">
        <a href="${pdfUrl}" target="_blank" class="btn-primary" style="padding: 8px 16px; font-size: 0.85rem; text-decoration: none;">
          <i data-lucide="download"></i> Download Original PDF
        </a>
        <button class="btn-secondary" onclick="closeModal('modal-product')">Close Viewer</button>
      </div>
    `;
    window.openModal('modal-product');
  };

  // Image Lightbox Modal
  window.openImageModal = function(imgUrl, title) {
    const body = document.getElementById('modal-product-body');
    body.innerHTML = `
      <h3 style="font-family: var(--font-heading); color: var(--cyan); font-size: 1.3rem; margin-bottom: 12px;">🖼️ ${title}</h3>
      
      <div style="width: 100%; max-height: 520px; border-radius: var(--radius-md); overflow: hidden; border: 1px solid var(--border-glass); text-align: center; background: rgba(0,0,0,0.5);">
        <img src="${imgUrl}" alt="${title}" style="max-width: 100%; max-height: 500px; object-fit: contain;">
      </div>

      <div style="display: flex; justify-content: flex-end; margin-top: 16px;">
        <button class="btn-secondary" onclick="closeModal('modal-product')">Close Lightbox</button>
      </div>
    `;
    window.openModal('modal-product');
  };

  window.openProductModal = function(productId) {
    const p = EVENT_DATA.products.find(item => item.id === productId);
    if (!p) return;

    const body = document.getElementById('modal-product-body');
    body.innerHTML = `
      <span class="product-badge" style="position: static; margin-bottom: 10px;">${p.badge}</span>
      <h2 style="font-family: var(--font-heading); font-size: 2rem; color: #fff; margin-bottom: 4px;">${p.name}</h2>
      <p style="color: var(--cyan); font-weight: 600; margin-bottom: 16px;">${p.tagline}</p>
      <p style="color: var(--text-secondary); line-height: 1.6; margin-bottom: 20px;">${p.description}</p>
      
      <div style="border-radius: var(--radius-md); overflow: hidden; margin-bottom: 20px;">
        <img src="${p.image}" alt="${p.name}" style="width: 100%; max-height: 280px; object-fit: cover;">
      </div>

      <h4 style="color: #fff; font-family: var(--font-heading); margin-bottom: 8px;">Key Capabilities:</h4>
      <ul class="product-features-list" style="margin-bottom: 20px;">
        ${p.features.map(f => `<li>${f}</li>`).join('')}
      </ul>

      <div style="display: flex; justify-content: flex-end; gap: 12px;">
        <button class="btn-secondary" onclick="closeModal('modal-product')">Close</button>
        <a href="${p.link}" target="_blank" class="btn-primary" style="padding: 10px 20px; font-size: 0.9rem;">
          <i data-lucide="external-link"></i> Open Live Demo
        </a>
      </div>
    `;
    window.openModal('modal-product');
  };

  // Detailed Team Member Modal
  window.openTeamMemberModal = function(memberId) {
    const allMembers = [...EVENT_DATA.teamGroups.inMeeting, ...EVENT_DATA.teamGroups.nonParticipants];
    const m = allMembers.find(item => item.id === memberId);
    if (!m) return;

    const isMeetingSpeaker = m.status.includes('In Meeting');
    const badgeColor = isMeetingSpeaker ? 'var(--emerald)' : 'var(--purple)';

    const body = document.getElementById('modal-product-body');
    body.innerHTML = `
      <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 20px;">
        <img src="${m.avatar}" alt="${m.name}" style="width: 70px; height: 70px; border-radius: 50%; object-fit: cover; border: 3px solid ${badgeColor};">
        <div>
          <span style="font-size: 0.75rem; font-weight: 700; background: rgba(255,255,255,0.06); color: ${badgeColor}; border: 1px solid ${badgeColor}; padding: 3px 10px; border-radius: 20px; display: inline-block; margin-bottom: 4px;">${m.status}</span>
          <h2 style="font-family: var(--font-heading); font-size: 1.8rem; color: #fff; margin-bottom: 2px;">${m.name}</h2>
          <p style="color: var(--cyan); font-weight: 600; font-size: 0.95rem;">${m.role} • ${m.company}</p>
        </div>
      </div>

      <div style="display: flex; flex-direction: column; gap: 14px; background: rgba(255,255,255,0.03); border: 1px solid var(--border-glass); padding: 16px; border-radius: var(--radius-md); margin-bottom: 20px;">
        <div>
          <h4 style="color: var(--cyan); font-family: var(--font-heading); font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px;">Professional Domain</h4>
          <p style="color: #fff; font-size: 0.95rem; font-weight: 500;">${m.professionDomain}</p>
        </div>

        <div>
          <h4 style="color: var(--cyan); font-family: var(--font-heading); font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 6px;">Key Professional Skills</h4>
          <div style="display: flex; flex-wrap: wrap; gap: 6px;">
            ${m.skills.map(s => `<span class="tech-tag" style="border-color: rgba(0,242,254,0.3); color: #fff;">${s}</span>`).join('')}
          </div>
        </div>

        <div>
          <h4 style="color: var(--gold); font-family: var(--font-heading); font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px;">Presentation Focus / Talk Track</h4>
          <p style="color: var(--text-secondary); font-size: 0.88rem; line-height: 1.5;">${m.presentationFocus}</p>
        </div>

        <div>
          <h4 style="color: var(--emerald); font-family: var(--font-heading); font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px;">Contribution to Runit</h4>
          <p style="color: var(--text-secondary); font-size: 0.88rem; line-height: 1.5;">${m.contribution}</p>
        </div>
      </div>

      <div style="display: flex; justify-content: flex-end;">
        <button class="btn-secondary" onclick="closeModal('modal-product')">Close Profile</button>
      </div>
    `;
    window.openModal('modal-product');
  };
});
