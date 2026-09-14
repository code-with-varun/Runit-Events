/**
 * RUNIT ONEVIA 2026 - Master Presentation & Event Site Engine
 * 25 Keynote Slides including Legal Credentials, Awards, Live Products, Data Tools & AI Media
 */

document.addEventListener('DOMContentLoaded', () => {
  if (window.lucide) {
    lucide.createIcons();
  }

  // State Variables
  let currentSlideIndex = 0;
  const totalSlides = 25;
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

  // Home Button Handlers (Return to Countdown Screen)
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

  document.getElementById('btn-save-timer').addEventListener('click', () => {
    const val = document.getElementById('timer-input-datetime').value;
    if (val) {
      targetDate = new Date(val);
      updateClockAndCountdown();
      window.closeModal('modal-timer');
    }
  });

  // ============================================================
  // 3. SLIDE DECK RENDERER (25 KEYNOTE SLIDES)
  // ============================================================
  const slideContentSlot = document.getElementById('slide-content-slot');
  const slideSelector = document.getElementById('slide-selector');
  const currentSlideNumLabel = document.getElementById('current-slide-num');
  const totalSlidesNumLabel = document.getElementById('total-slides-num');
  const progressBar = document.getElementById('progress-bar');

  totalSlidesNumLabel.textContent = totalSlides;

  // Dropdown Options (25 slides total, new additions inserted right after Slide 8)
  const slideTitles = [
    "1. RUNIT ONEVIA 2026",
    "2. Welcome Message",
    "3. Event Agenda",
    "4. Why Runit Exists",
    "5. About Runit Infotech",
    "6. Our Journey",
    "7. Mission & Vision",
    "8. Organization Overview & Community Vision",
    "9. Legal Registrations & Credentials",
    "10. Team Recognition & Awards",
    "11. Flagship Live Deployments",
    "12. Data Tools & Desktop Utilities",
    "13. AI Motion Graphics & Creative Media",
    "14. Process Automation & Publishing",
    "15. Product Portfolio Overview",
    "16. Orbita Showcase",
    "17. PowerBooks Showcase",
    "18. MoneyMatrix Rebrand",
    "19. ISPARK Platform",
    "20. MIS Reporting Services",
    "21. 5-Year Product Roadmap",
    "22. Product Strategy Matrix",
    "23. Team & Core Community Roster",
    "24. Quarterly Rhythm & Objectives",
    "25. Thank You & Open Discussion"
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

  function renderSlide(index) {
    let html = '';
    const d = EVENT_DATA;

    switch (index) {
      case 0: // Cover Slide
        html = `
          <div class="slide-header">
            <span class="slide-tag">Annual Flagship Keynote</span>
            <span class="slide-number">Slide 1 of 25</span>
          </div>
          <div style="text-align: center; margin: auto 0;">
            <div style="font-family: var(--font-heading); font-size: 1.8rem; font-weight: 900; letter-spacing: 3px; color: var(--cyan); text-transform: uppercase; margin-bottom: 12px;">
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

      case 1: // Welcome Message
        html = `
          <div class="slide-header">
            <span class="slide-tag">Founder Opening</span>
            <span class="slide-number">Slide 2 of 25</span>
          </div>
          <h2 class="slide-title">Welcome to RUNIT ONEVIA 2026</h2>
          <p class="slide-subtitle">Building Tomorrow, Starting Today — Transforming ideas into products & products into solutions.</p>
          <div class="grid-2" style="margin-top: 10px;">
            <div class="content-card" style="border-left: 4px solid var(--cyan);">
              <h3 class="content-card-title" style="color: var(--cyan);">Opening Keynote Statement</h3>
              <p class="content-card-text" style="font-size: 1.05rem; font-style: italic; line-height: 1.7; color: #fff;">
                "Runit was created to transform ideas into products, products into solutions, and solutions into businesses. Today we are a small group of professionals from diverse industries. Tomorrow we aim to build products that help businesses improve productivity, automation, reporting, and decision-making. Runit Onevia 2026 marks the beginning of that journey."
              </p>
              <div style="margin-top: 16px; font-size: 0.85rem; color: var(--text-muted); font-weight: bold;">
                — Varun Akshay, Founder & Product Lead
              </div>
            </div>
            <div class="content-card">
              <h3 class="content-card-title">Event Purpose & Milestones</h3>
              <ul style="list-style: none; display: flex; flex-direction: column; gap: 12px; margin-top: 12px;">
                <li style="display: flex; gap: 10px; font-size: 0.95rem; color: var(--text-secondary);">
                  <i data-lucide="check-circle-2" style="color: var(--emerald); flex-shrink: 0;"></i>
                  <span>Establish <strong>Runit Onevia</strong> as our annual flagship gathering for vision, community, and roadmaps.</span>
                </li>
                <li style="display: flex; gap: 10px; font-size: 0.95rem; color: var(--text-secondary);">
                  <i data-lucide="check-circle-2" style="color: var(--emerald); flex-shrink: 0;"></i>
                  <span>Launch <strong>Runit Nextora</strong> as our quarterly rhythm of execution, progress updates, and product sprints.</span>
                </li>
                <li style="display: flex; gap: 10px; font-size: 0.95rem; color: var(--text-secondary);">
                  <i data-lucide="check-circle-2" style="color: var(--emerald); flex-shrink: 0;"></i>
                  <span>Create a lasting public record of our engineering & product building journey.</span>
                </li>
              </ul>
            </div>
          </div>
        `;
        break;

      case 2: // Event Agenda
        html = `
          <div class="slide-header">
            <span class="slide-tag">Session Flow</span>
            <span class="slide-number">Slide 3 of 25</span>
          </div>
          <h2 class="slide-title">Event Agenda & Schedule</h2>
          <p class="slide-subtitle">Structured 30-Minute Founder Keynote followed by Interactive Community Presentations.</p>
          <div class="grid-2" style="margin-top: 10px;">
            <div class="content-card">
              <h3 class="content-card-title" style="color: var(--cyan);"><i data-lucide="mic"></i> Part 1: Founder Keynote (30 Mins)</h3>
              <ol style="margin-left: 20px; color: var(--text-secondary); display: flex; flex-direction: column; gap: 8px; font-size: 0.9rem;">
                <li>Welcome & Objectives (2 Mins)</li>
                <li>What is Runit Infotech? (3 Mins)</li>
                <li>Our Journey & Milestone Growth (3 Mins)</li>
                <li>Organization Overview & Community (3 Mins)</li>
                <li>Legal Credentials, Awards & Asset Showcase (5 Mins)</li>
                <li>Product Portfolio Showcase (10 Mins)</li>
                <li>5-Year Strategy & Roadmap (5 Mins)</li>
                <li>Immediate Action Items (2 Mins)</li>
              </ol>
            </div>
            <div class="content-card">
              <h3 class="content-card-title" style="color: var(--purple);"><i data-lucide="users"></i> Part 2: Community Voices (5 Mins / Speaker)</h3>
              <p class="content-card-text" style="margin-bottom: 12px;">
                Every core member presents their background, domain expertise, business insights, and contribution to Runit.
              </p>
              <div style="background: rgba(0,0,0,0.3); padding: 12px; border-radius: var(--radius-sm); border: 1px solid var(--border-glass);">
                <span style="font-size: 0.8rem; font-weight: bold; color: var(--gold);">Followed by:</span>
                <p style="font-size: 0.85rem; color: #fff; margin-top: 4px;">Part 3: Open Discussion & Strategic Q&A</p>
              </div>
            </div>
          </div>
        `;
        break;

      case 3: // Why Runit Exists
        html = `
          <div class="slide-header">
            <span class="slide-tag">Strategic Foundation</span>
            <span class="slide-number">Slide 4 of 25</span>
          </div>
          <h2 class="slide-title">Why Runit Exists: Six Core Objectives</h2>
          <p class="slide-subtitle">Runit is not just about coding. Runit is about solving real business problems using technology.</p>
          <div class="grid-3" style="margin-top: 10px;">
            ${d.objectives.map(o => `
              <div class="content-card">
                <h4 style="font-family: var(--font-heading); font-size: 1.05rem; font-weight: 700; color: var(--cyan); margin-bottom: 6px;">${o.title}</h4>
                <p style="font-size: 0.85rem; color: var(--text-secondary); line-height: 1.4;">${o.desc}</p>
              </div>
            `).join('')}
          </div>
        `;
        break;

      case 4: // About Runit Infotech
        html = `
          <div class="slide-header">
            <span class="slide-tag">Company Vision</span>
            <span class="slide-number">Slide 5 of 25</span>
          </div>
          <h2 class="slide-title">About Runit Infotech & Focus Areas</h2>
          <p class="slide-subtitle">A product-focused initiative building scalable solutions across 7 technology domains.</p>
          <div class="grid-4" style="margin-top: 10px;">
            ${d.focusAreas.map(f => `
              <div class="content-card" style="text-align: center; padding: 16px 12px;">
                <div style="width: 42px; height: 42px; background: rgba(0,242,254,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px auto; color: var(--cyan);">
                  <i data-lucide="${f.icon}"></i>
                </div>
                <h4 style="font-family: var(--font-heading); font-size: 0.95rem; font-weight: 700; color: #fff; margin-bottom: 4px;">${f.name}</h4>
                <p style="font-size: 0.78rem; color: var(--text-muted);">${f.desc}</p>
              </div>
            `).join('')}
          </div>
          <div style="margin-top: 20px; background: var(--gradient-cyan-purple); padding: 14px; border-radius: var(--radius-md); text-align: center; font-family: var(--font-heading); font-weight: 800; font-size: 1.15rem; color: #fff; letter-spacing: 1px;">
            Long-Term Vision: MIS + Automation + AI + SaaS + Business Solutions
          </div>
        `;
        break;

      case 5: // Our Journey
        html = `
          <div class="slide-header">
            <span class="slide-tag">Milestones</span>
            <span class="slide-number">Slide 6 of 25</span>
          </div>
          <h2 class="slide-title">Our Growth Journey</h2>
          <p class="slide-subtitle">Every experiment, learning cycle, and innovation milestone shapes the organization we are building today.</p>
          <div class="grid-3" style="margin-top: 10px;">
            ${d.journey.map(j => `
              <div class="content-card" style="position: relative; overflow: hidden;">
                <span style="font-size: 0.75rem; font-weight: 700; color: var(--cyan); text-transform: uppercase;">${j.phase}</span>
                <h4 style="font-family: var(--font-heading); font-size: 1.1rem; font-weight: 700; color: #fff; margin: 4px 0 8px 0;">${j.title}</h4>
                <p style="font-size: 0.85rem; color: var(--text-secondary);">${j.desc}</p>
              </div>
            `).join('')}
          </div>
        `;
        break;

      case 6: // Mission & Vision
        html = `
          <div class="slide-header">
            <span class="slide-tag">Core Strategy</span>
            <span class="slide-number">Slide 7 of 25</span>
          </div>
          <h2 class="slide-title">Mission & Guiding Principles</h2>
          <p class="slide-subtitle">Transforming raw business complexity into elegant, automated technology platforms.</p>
          <div class="grid-2" style="margin-top: 16px;">
            <div class="content-card" style="padding: 30px;">
              <h3 style="font-family: var(--font-heading); color: var(--cyan); font-size: 1.4rem; margin-bottom: 12px;">Our Mission</h3>
              <p style="font-size: 1.05rem; color: var(--text-secondary); line-height: 1.6;">
                To create digital products that solve operational bottlenecks, streamline MIS reporting, empower small business owners, and introduce intelligent AI automation into daily decision-making.
              </p>
            </div>
            <div class="content-card" style="padding: 30px;">
              <h3 style="font-family: var(--font-heading); color: var(--purple); font-size: 1.4rem; margin-bottom: 12px;">Guiding Principle</h3>
              <p style="font-size: 1.05rem; color: #fff; font-weight: 600; line-height: 1.6;">
                "Software design without business impact is meaningless. At Runit, we engineer software specifically to produce measurable business value."
              </p>
            </div>
          </div>
        `;
        break;

      case 7: // Slide 8: Organization Overview & Community Vision
        html = `
          <div class="slide-header">
            <span class="slide-tag">Organization Overview</span>
            <span class="slide-number">Slide 8 of 25</span>
          </div>
          <h2 class="slide-title">Organization Structure & Vision</h2>
          <p class="slide-subtitle">Runit currently operates as a founder-led initiative supported by a multidisciplinary core community.</p>
          <div class="grid-3" style="margin-top: 16px;">
            <div class="content-card" style="padding: 24px; border-top: 4px solid var(--cyan);">
              <div style="width: 44px; height: 44px; background: rgba(0,242,254,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 12px; color: var(--cyan);">
                <i data-lucide="user-check"></i>
              </div>
              <h3 style="font-family: var(--font-heading); font-size: 1.2rem; color: #fff; margin-bottom: 6px;">Founder-Led Direction</h3>
              <p style="font-size: 0.88rem; color: var(--text-secondary); line-height: 1.5;">
                Led by <strong>Varun Akshay</strong> (Founder & Product Lead), steering engineering architecture, product design, and strategic roadmaps.
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

      // ============================================================
      // NEW ADDITION SLIDES (SLIDES 9 - 14) PLACED RIGHT AFTER SLIDE 8
      // ============================================================

      case 8: // Slide 9: Legal Registrations & Official Credentials
        html = `
          <div class="slide-header">
            <span class="slide-tag">Legal Compliance & Governance</span>
            <span class="slide-number">Slide 9 of 25</span>
          </div>
          <h2 class="slide-title">Legal Registrations & Official Credentials</h2>
          <p class="slide-subtitle">Official company registrations, MSME Udyam credentials & MCA ROC certificates. Click any PDF card to view the document.</p>
          
          <div class="grid-3" style="margin-top: 16px;">
            ${d.newAdditions.legalRegistrations.map(item => `
              <div class="content-card asset-card">
                <div>
                  <span class="product-badge" style="position: static; display: inline-block; margin-bottom: 8px;">${item.category}</span>
                  <h3 style="font-family: var(--font-heading); font-size: 1.15rem; font-weight: 700; color: #fff; margin-bottom: 6px;">${item.title}</h3>
                  <p style="font-size: 0.85rem; color: var(--text-secondary); line-height: 1.4;">${item.desc}</p>
                </div>
                
                <div style="margin-top: 16px; display: flex; flex-direction: column; gap: 8px;">
                  <div class="pdf-preview-box" onclick="openPdfModal('${item.file}', '${item.title}')">
                    <i data-lucide="file-text" style="color: var(--cyan); width: 24px; height: 24px;"></i>
                    <div style="font-size: 0.85rem; color: #fff; font-weight: 600;">View ROC / Registration Certificate (PDF)</div>
                  </div>
                  ${item.panFile ? `
                    <div class="pdf-preview-box" style="border-color: var(--purple);" onclick="openPdfModal('${item.panFile}', '${item.title} - PAN Document')">
                      <i data-lucide="credit-card" style="color: var(--purple); width: 24px; height: 24px;"></i>
                      <div style="font-size: 0.85rem; color: #fff; font-weight: 600;">View PAN Card Document (PDF)</div>
                    </div>
                  ` : ''}
                </div>
              </div>
            `).join('')}
          </div>
        `;
        break;

      case 9: // Slide 10: Team Achievements & Recognition Awards
        html = `
          <div class="slide-header">
            <span class="slide-tag">Team Excellence</span>
            <span class="slide-number">Slide 10 of 25</span>
          </div>
          <h2 class="slide-title">Team Recognition & Excellence Awards</h2>
          <p class="slide-subtitle">Honoring outstanding contributions across software architecture, AI automation, and MIS excellence. Click any certificate to enlarge.</p>
          
          <div class="grid-3" style="margin-top: 16px;">
            ${d.newAdditions.awards.map(a => `
              <div class="content-card asset-card">
                <div>
                  <span style="font-size: 0.75rem; font-weight: 700; color: var(--gold); background: rgba(255,215,0,0.1); border: 1px solid var(--gold); padding: 2px 10px; border-radius: 12px; display: inline-block; margin-bottom: 8px;">${a.category}</span>
                  <h3 style="font-family: var(--font-heading); font-size: 1.2rem; color: #fff; margin-bottom: 2px;">${a.recipient}</h3>
                  <p style="color: var(--cyan); font-weight: 600; font-size: 0.9rem; margin-bottom: 8px;">${a.title}</p>
                  <p style="font-size: 0.83rem; color: var(--text-secondary); line-height: 1.4;">${a.desc}</p>
                </div>

                <div style="margin-top: 14px; border-radius: var(--radius-sm); overflow: hidden; border: 1px solid var(--border-glass); cursor: pointer;" onclick="openImageModal('${a.image}', '${a.recipient} - ${a.title}')">
                  <img src="${a.image}" alt="${a.title}" style="width: 100%; height: 160px; object-fit: cover;" onerror="this.src='https://via.placeholder.com/400x200/10172A/00F2FE?text=Award+Certificate'">
                  <div style="background: rgba(0,0,0,0.7); text-align: center; padding: 6px; font-size: 0.78rem; color: var(--cyan); font-weight: 600;">
                    🔍 Click to inspect original award certificate
                  </div>
                </div>
              </div>
            `).join('')}
          </div>
        `;
        break;

      case 10: // Slide 11: Flagship Live Deployments & Web Sites
        html = `
          <div class="slide-header">
            <span class="slide-tag">Live Product Ecosystem</span>
            <span class="slide-number">Slide 11 of 25</span>
          </div>
          <h2 class="slide-title">Flagship Live Deployments & Web Platforms</h2>
          <p class="slide-subtitle">Active web applications, live SaaS tools, e-commerce brand portals & interactive games with production URLs.</p>
          
          <div class="grid-3" style="margin-top: 14px;">
            ${d.newAdditions.liveDeployments.map(p => `
              <div class="content-card asset-card">
                <div>
                  <span class="product-badge" style="position: static; display: inline-block; margin-bottom: 6px;">${p.category}</span>
                  <h3 style="font-family: var(--font-heading); font-size: 1.15rem; color: #fff; margin-bottom: 4px;">${p.name}</h3>
                  <p style="font-size: 0.83rem; color: var(--text-secondary); margin-bottom: 8px;">${p.desc}</p>
                  
                  ${p.screenshots ? `
                    <div class="asset-thumb-grid">
                      ${p.screenshots.map(s => `
                        <img src="${s}" class="asset-thumb" alt="Screenshot" onclick="openImageModal('${s}', '${p.name} Screenshot')">
                      `).join('')}
                    </div>
                  ` : ''}
                </div>

                <div style="margin-top: 12px; display: flex; align-items: center; justify-content: space-between; border-top: 1px solid var(--border-glass); padding-top: 10px;">
                  <div style="display: flex; gap: 4px; flex-wrap: wrap;">
                    ${p.tech.map(t => `<span class="tech-tag" style="font-size: 0.7rem; padding: 2px 6px;">${t}</span>`).join('')}
                  </div>
                  <a href="${p.link}" target="_blank" class="btn-primary" style="padding: 6px 14px; font-size: 0.8rem; text-decoration: none;">
                    <i data-lucide="external-link" style="width: 14px; height: 14px;"></i> Open Live Site
                  </a>
                </div>
              </div>
            `).join('')}
          </div>
        `;
        break;

      case 11: // Slide 12: Data Tools, Desktop Engines & Utilities
        html = `
          <div class="slide-header">
            <span class="slide-tag">Utilities & Data Engines</span>
            <span class="slide-number">Slide 12 of 25</span>
          </div>
          <h2 class="slide-title">Data Tools, Desktop Utilities & Browser Extensions</h2>
          <p class="slide-subtitle">Personal finance dashboards, native desktop time tracking software & Google Chrome web extensions.</p>
          
          <div class="grid-2" style="margin-top: 14px;">
            ${d.newAdditions.toolsAndUtilities.map(t => `
              <div class="content-card asset-card">
                <div>
                  <span style="font-size: 0.75rem; font-weight: 700; color: var(--purple); background: rgba(127,0,255,0.1); border: 1px solid var(--purple); padding: 2px 10px; border-radius: 12px; display: inline-block; margin-bottom: 6px;">${t.category}</span>
                  <h3 style="font-family: var(--font-heading); font-size: 1.2rem; color: #fff; margin-bottom: 4px;">${t.name}</h3>
                  <p style="font-size: 0.85rem; color: var(--text-secondary); line-height: 1.4;">${t.desc}</p>
                </div>

                ${t.screenshots ? `
                  <div class="asset-thumb-grid" style="grid-template-columns: repeat(5, 1fr); margin-top: 10px;">
                    ${t.screenshots.map(s => `
                      <img src="${s}" class="asset-thumb" style="height: 55px;" alt="${t.name}" onclick="openImageModal('${s}', '${t.name}')">
                    `).join('')}
                  </div>
                ` : ''}

                ${t.file ? `
                  <div class="pdf-preview-box" style="margin-top: 12px;" onclick="openPdfModal('${t.file}', '${t.name}')">
                    <i data-lucide="file-text" style="color: var(--cyan); width: 22px; height: 22px;"></i>
                    <div style="font-size: 0.85rem; color: #fff; font-weight: 600;">View Specification & User Guide (PDF)</div>
                  </div>
                ` : ''}
              </div>
            `).join('')}
          </div>
        `;
        break;

      case 12: // Slide 13: AI Motion Graphics & Creative Media
        html = `
          <div class="slide-header">
            <span class="slide-tag">Generative AI Media</span>
            <span class="slide-number">Slide 13 of 25</span>
          </div>
          <h2 class="slide-title">AI Motion Graphics & Creative Media</h2>
          <p class="slide-subtitle">Generative AI visual shorts (Thukalgal, Kavasam, Signout, Valaiyosai, im Moon), photorealistic character consistency & wedding story simulations.</p>
          
          <div class="grid-3" style="margin-top: 14px;">
            <div class="content-card asset-card">
              <div>
                <h4 style="font-family: var(--font-heading); font-size: 1.05rem; color: var(--cyan); margin-bottom: 6px;">AI Motion Graphic Shorts</h4>
                <p style="font-size: 0.8rem; color: var(--text-secondary);">Cinematic generative AI visual stories & motion posters.</p>
              </div>
              <div style="display: flex; flex-direction: column; gap: 8px; margin-top: 10px; max-height: 200px; overflow-y: auto;">
                ${d.newAdditions.aiCreativeMedia.shorts.map(s => `
                  <div style="background: rgba(255,255,255,0.03); border: 1px solid var(--border-glass); padding: 8px; border-radius: var(--radius-sm);">
                    <div style="font-size: 0.8rem; color: #fff; font-weight: 600; margin-bottom: 4px;">${s.title} (${s.images.length} frames)</div>
                    <div style="display: flex; gap: 4px; overflow-x: auto;">
                      ${s.images.map(img => `
                        <img src="${img}" style="width: 50px; height: 40px; object-fit: cover; border-radius: 4px; cursor: pointer;" onclick="openImageModal('${img}', '${s.title}')">
                      `).join('')}
                    </div>
                  </div>
                `).join('')}
              </div>
            </div>

            <div class="content-card asset-card">
              <div>
                <h4 style="font-family: var(--font-heading); font-size: 1.05rem; color: var(--purple); margin-bottom: 6px;">AI Wedding Story Simulations</h4>
                <p style="font-size: 0.8rem; color: var(--text-secondary);">Photorealistic AI character consistency & storytelling simulations.</p>
              </div>
              <div class="asset-thumb-grid" style="grid-template-columns: repeat(3, 1fr); margin-top: 10px;">
                ${d.newAdditions.aiCreativeMedia.weddingSimulations.map((img, i) => `
                  <img src="${img}" class="asset-thumb" alt="Wedding Simulation ${i+1}" onclick="openImageModal('${img}', 'AI Wedding Story Simulation ${i+1}')">
                `).join('')}
              </div>
            </div>

            <div class="content-card asset-card">
              <div>
                <h4 style="font-family: var(--font-heading); font-size: 1.05rem; color: var(--gold); margin-bottom: 6px;">Video Editing & Visual Remixes</h4>
                <p style="font-size: 0.8rem; color: var(--text-secondary);">Creative video editing, composite keying, and audio-visual remixes.</p>
              </div>
              <div class="asset-thumb-grid" style="grid-template-columns: repeat(3, 1fr); margin-top: 10px;">
                ${d.newAdditions.aiCreativeMedia.remixes.map((img, i) => `
                  <img src="${img}" class="asset-thumb" alt="Video Remix ${i+1}" onclick="openImageModal('${img}', 'Video Remix ${i+1}')">
                `).join('')}
              </div>
            </div>
          </div>
        `;
        break;

      case 13: // Slide 14: Process Automation & Digital Publishing
        html = `
          <div class="slide-header">
            <span class="slide-tag">Enterprise Operations</span>
            <span class="slide-number">Slide 14 of 25</span>
          </div>
          <h2 class="slide-title">Process Automation & Digital Publishing</h2>
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

      // ============================================================
      // EXISTING PORTFOLIO & ROADMAP SLIDES (SLIDES 15 - 25)
      // ============================================================

      case 14: // Product Portfolio Overview
        html = `
          <div class="slide-header">
            <span class="slide-tag">Product Ecosystem</span>
            <span class="slide-number">Slide 15 of 25</span>
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

      case 15: // Orbita Spotlight
        html = renderProductSlide(d.products[0], 16);
        break;

      case 16: // PowerBooks Spotlight
        html = renderProductSlide(d.products[1], 17);
        break;

      case 17: // MoneyMatrix Spotlight
        html = `
          <div class="slide-header">
            <span class="slide-tag">Product Rebrand & Spotlight</span>
            <span class="slide-number">Slide 18 of 25</span>
          </div>
          <div class="product-spotlight">
            <div class="product-info">
              <div>
                <span class="product-badge" style="position: static; background: var(--pink); border-color: var(--pink); color: #fff;">${d.products[2].badge}</span>
                <span style="font-size: 0.85rem; color: var(--gold); margin-left: 10px; font-weight: 600;">${d.products[2].rebrandNote}</span>
              </div>
              <h2 class="slide-title" style="margin-bottom: 4px;">${d.products[2].name}</h2>
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
              <img src="${d.products[2].image}" alt="MoneyMatrix Screenshot" class="product-img" onerror="this.src='https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?w=800&auto=format&fit=crop&q=80'">
            </div>
          </div>
        `;
        break;

      case 18: // ISPARK Spotlight
        html = renderProductSlide(d.products[3], 19);
        break;

      case 19: // MIS Services Spotlight
        html = renderProductSlide(d.products[4], 20);
        break;

      case 20: // 5-Year Roadmap
        html = `
          <div class="slide-header">
            <span class="slide-tag">Strategic Vision</span>
            <span class="slide-number">Slide 21 of 25</span>
          </div>
          <h2 class="slide-title">5-Year Strategic Product & Execution Roadmap</h2>
          <p class="slide-subtitle">Phased execution matrix from 2026 foundation through 2030+ industry leadership.</p>
          <div class="timeline-matrix" style="margin-top: 10px;">
            ${d.roadmap.map(r => `
              <div class="timeline-phase">
                <div class="timeline-header">${r.phase}</div>
                <div class="timeline-years">${r.years}</div>
                <h4 style="font-family: var(--font-heading); font-size: 0.95rem; font-weight: 700; color: #fff; margin-bottom: 8px;">${r.title}</h4>
                <ul class="timeline-list">
                  ${r.items.map(item => `<li>${item}</li>`).join('')}
                </ul>
              </div>
            `).join('')}
          </div>
        `;
        break;

      case 21: // Product Strategy Matrix
        html = `
          <div class="slide-header">
            <span class="slide-tag">Market Verticals</span>
            <span class="slide-number">Slide 22 of 25</span>
          </div>
          <h2 class="slide-title">Product Strategy & Sector Solutions</h2>
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

      case 22: // Team & Core Community Roster
        html = `
          <div class="slide-header">
            <span class="slide-tag">Member Presentations</span>
            <span class="slide-number">Slide 23 of 25</span>
          </div>
          <h2 class="slide-title" style="margin-bottom: 4px;">Team & Core Community Roster</h2>
          <p class="slide-subtitle" style="margin-bottom: 16px;">Click any team member card to view their profession-tailored presentation content & talk track.</p>
          
          <div style="display: flex; flex-direction: column; gap: 20px; max-height: 420px; overflow-y: auto; padding-right: 8px;">
            <!-- Group 1: In Meeting Participants -->
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
                      <div class="team-role" style="color: var(--cyan);">${m.role}</div>
                      <div class="team-company">${m.company}</div>
                    </div>
                  </div>
                `).join('')}
              </div>
            </div>

            <!-- Group 2: Non-Participant Team Members -->
            <div>
              <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 10px;">
                <span style="width: 10px; height: 10px; background: var(--purple); border-radius: 50%; display: inline-block;"></span>
                <h3 style="font-family: var(--font-heading); font-size: 1.1rem; color: var(--purple); font-weight: 700;">Non Participant Team Members</h3>
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

      case 23: // Immediate Objectives & Runit Nextora Cadence
        html = `
          <div class="slide-header">
            <span class="slide-tag">Execution Cadence</span>
            <span class="slide-number">Slide 24 of 25</span>
          </div>
          <h2 class="slide-title">Immediate Objectives & Runit Nextora Cadence</h2>
          <p class="slide-subtitle">Next steps immediately following Runit Onevia 2026.</p>
          <div class="grid-2" style="margin-top: 10px;">
            <div class="content-card">
              <h3 style="color: var(--cyan); font-family: var(--font-heading); margin-bottom: 12px;"><i data-lucide="target"></i> Immediate Post-Event Goals</h3>
              <ul style="list-style: none; display: flex; flex-direction: column; gap: 8px;">
                ${d.immediateObjectives.map(obj => `
                  <li style="display: flex; gap: 10px; font-size: 0.85rem; color: var(--text-secondary);">
                    <i data-lucide="check" style="color: var(--emerald); flex-shrink: 0;"></i> ${obj}
                  </li>
                `).join('')}
              </ul>
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

      case 24: // Slide 25: Thank You & Grand Finale
        html = `
          <div class="slide-header">
            <span class="slide-tag">Grand Finale</span>
            <span class="slide-number">Slide 25 of 25</span>
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
        <span class="slide-number">Slide ${slideNum} of 25</span>
      </div>
      <div class="product-spotlight">
        <div class="product-info">
          <div>
            <span class="product-badge" style="position: static;">${p.badge}</span>
            <span style="font-size: 0.85rem; color: var(--cyan); margin-left: 10px; font-weight: 600;">${p.category}</span>
          </div>
          <h2 class="slide-title" style="margin-bottom: 4px;">${p.name}</h2>
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
          <img src="${p.image}" alt="${p.name} Screenshot" class="product-img" onerror="this.src='https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800&auto=format&fit=crop&q=80'">
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
