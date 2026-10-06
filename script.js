/* ============================================================
   script.js — الموقع الرئيسي
   يحمّل البيانات من API ويحدّث الموقع تلقائياً
   ============================================================ */

(function () {
    'use strict';

    const API = '/api';

    const $  = (s) => document.querySelector(s);
    const $$ = (s) => document.querySelectorAll(s);

    const esc = (s) => s == null ? '' : String(s)
        .replace(/&/g, '&amp;').replace(/</g, '&lt;')
        .replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');

    /* ---------- جلب البيانات ---------- */
    async function fetchData(endpoint) {
        try {
            const res = await fetch(API + '/' + endpoint);
            if (!res.ok) return null;
            return await res.json();
        } catch (e) {
            console.warn('فشل تحميل', endpoint, e);
            return null;
        }
    }

    /* ---------- 1) الخدمات ---------- */
    function renderServices(list) {
        if (!Array.isArray(list) || !list.length) return;
        const grid = $('.services-grid');
        if (!grid) return;

        grid.innerHTML = list.map((s, i) => `
            <div class="service-card" data-aos="fade-up" data-aos-delay="${(i % 3) * 100}">
                <div class="card-icon"><i class="${esc(s.icon)}"></i></div>
                <h3>${esc(s.title)}</h3>
                <p>${esc(s.description)}</p>
            </div>
        `).join('');
    }

    /* ---------- 2) الإحصاءات ---------- */
    function renderStats(list) {
        if (!Array.isArray(list) || !list.length) return;
        const grid = $('.stats-grid');
        if (!grid) return;

        grid.innerHTML = list.map((s, i) => `
            <div class="stat-item" data-aos="zoom-in" data-aos-delay="${i * 200}">
                <h3 class="counter" data-target="${Number(s.value) || 0}">0</h3>
                <p>${esc(s.label)}</p>
            </div>
        `).join('');

        rebindCounters();
    }

    /* ---------- 3) آراء العملاء ---------- */
    function renderTestimonials(list) {
        if (!Array.isArray(list) || !list.length) return;
        const grid = $('.testi-grid');
        if (!grid) return;

        grid.innerHTML = list.map((t, i) => {
            const stars = Array.from({ length: 5 }, (_, k) =>
                `<i class="fa${k < (t.stars || 5) ? 's' : 'r'} fa-star"></i>`
            ).join('');
            return `
                <div class="testi-card" data-aos="${i % 2 === 0 ? 'fade-right' : 'fade-left'}">
                    <div class="stars">${stars}</div>
                    <p>"${esc(t.text)}"</p>
                    <h4>- ${esc(t.name)}</h4>
                </div>
            `;
        }).join('');
    }

    /* ---------- 4) المعرض ---------- */
    function renderGallery(list) {
        if (!Array.isArray(list) || !list.length) return;
        const grid = $('.bento-grid');
        if (!grid) return;

        const aosCycle = ['zoom-in', 'fade-right', 'fade-up', 'fade-left'];

        grid.innerHTML = list.map((item, i) => {
            const sizeClass = item.size === 'wide' ? ' item-wide'
                            : item.size === 'tall' ? ' item-tall' : '';
            const aos = aosCycle[i % aosCycle.length];

            if (item.type === 'video') {
                const isAuto = i === 0;
                const icon = (item.label || '').includes('لمسات') ? 'fa-magic' : 'fa-play-circle';
                return `
                    <div class="bento-item${sizeClass} video-hover-play" data-aos="${aos}">
                        <video ${isAuto ? 'autoplay ' : ''}muted loop playsinline class="bento-video">
                            <source src="${esc(item.src)}" type="video/mp4">
                        </video>
                        <div class="bento-overlay">
                            ${item.label ? `<span><i class="fas ${icon}"></i> ${esc(item.label)}</span>` : ''}
                            <h3>${esc(item.title)}</h3>
                        </div>
                    </div>
                `;
            }

            return `
                <div class="bento-item${sizeClass}" data-aos="${aos}">
                    <img src="${esc(item.src)}" alt="${esc(item.title)}">
                    <div class="bento-overlay">
                        ${item.label ? `<span>${esc(item.label)}</span>` : ''}
                        <h3>${esc(item.title)}</h3>
                    </div>
                </div>
            `;
        }).join('');

        rebindHoverVideos();
    }

    /* ---------- 5) الإعدادات (الاتصال) ---------- */
    function renderSettings(s) {
        if (!s) return;

        if (s.phone) {
            $$('a[href^="tel:"]').forEach(a => {
                const href = a.getAttribute('href') || '';
                const intl = href.includes('00966') || href.includes('+966');
                a.setAttribute('href', 'tel:' + (intl ? '00966' + s.phone.replace(/^0/, '') : s.phone));
            });

            $$('.footer-col p').forEach(p => {
                if (p.querySelector('i.fa-phone')) {
                    p.innerHTML = `<i class="fas fa-phone"></i> ${esc(s.phone)}`;
                }
            });
        }

        if (s.whatsapp) {
            $$('a[href*="wa.me"]').forEach(a => {
                a.setAttribute('href', 'https://wa.me/' + s.whatsapp);
            });
        }

        if (s.hours) {
            $$('.footer-col p').forEach(p => {
                if (p.querySelector('i.fa-tools')) {
                    p.innerHTML = `<i class="fas fa-tools"></i> ${esc(s.hours)}`;
                }
            });
        }
    }

    /* ---------- العدادات ---------- */
    function rebindCounters() {
        const counters = $$('.counter');
        if (!counters.length) return;

        const speed = 200;
        const start = (el) => {
            if (el.dataset.counted === '1') return;
            el.dataset.counted = '1';
            const target = Number(el.getAttribute('data-target')) || 0;
            let current = 0;
            const step = target / speed;
            const tick = () => {
                current += step;
                if (current < target) {
                    el.innerText = Math.ceil(current);
                    requestAnimationFrame(tick);
                } else {
                    el.innerText = target;
                }
            };
            tick();
        };

        if ('IntersectionObserver' in window) {
            const obs = new IntersectionObserver((entries) => {
                entries.forEach(e => {
                    if (e.isIntersecting) { start(e.target); obs.unobserve(e.target); }
                });
            }, { threshold: 0.5 });
            counters.forEach(c => obs.observe(c));
        } else {
            counters.forEach(start);
        }
    }

    /* ---------- فيديوهات hover ---------- */
    function rebindHoverVideos() {
        $$('.video-hover-play').forEach(item => {
            if (item.dataset.syncBound === '1') return;
            item.dataset.syncBound = '1';
            const video = item.querySelector('video');
            if (!video) return;
            item.addEventListener('mouseenter', () => video.play().catch(() => {}));
            item.addEventListener('mouseleave', () => {
                video.pause();
                video.currentTime = 0;
            });
        });
    }

    /* ---------- قائمة الموبايل ---------- */
    function initMenu() {
        const menuBtn = $('.menu-btn');
        const navLinks = $('.nav-links-centered');
        if (menuBtn && navLinks) {
            menuBtn.addEventListener('click', () => {
                navLinks.classList.toggle('active');
            });
        }
    }

    /* ---------- التشغيل ---------- */
    async function init() {
        initMenu();

        // AOS
        if (window.AOS) {
            AOS.init({ duration: 1000, once: false });
        }

        // جلب كل البيانات بالتوازي
        const [services, gallery, testimonials, stats, settings] = await Promise.all([
            fetchData('services.php'),
            fetchData('gallery.php'),
            fetchData('testimonials.php'),
            fetchData('stats.php'),
            fetchData('settings.php')
        ]);

        if (services) renderServices(services);
        if (gallery) renderGallery(gallery);
        if (testimonials) renderTestimonials(testimonials);
        if (stats) renderStats(stats);
        if (settings) renderSettings(settings);

        // تحديث AOS بعد إدراج العناصر
        if (window.AOS && typeof AOS.refreshHard === 'function') {
            AOS.refreshHard();
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();