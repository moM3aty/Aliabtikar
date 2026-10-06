/* ============================================================
   sync.js — مزامنة محتوى الموقع مع لوحة التحكم
   يقرأ البيانات من localStorage ويعيد بناء المحتوى بنفس
   البنية والكلاسات الأصلية — بدون أي تعديل على CSS
   ============================================================ */
(function () {
    'use strict';

    const LS_KEY = 'alibtikar_admin_db_v1';

    let db;
    try {
        const raw = localStorage.getItem(LS_KEY);
        if (!raw) return;              // لا توجد بيانات مخصصة → اترك الموقع كما هو
        db = JSON.parse(raw);
        if (!db || typeof db !== 'object') return;
    } catch (e) {
        console.warn('Sync: تعذر قراءة بيانات لوحة التحكم', e);
        return;
    }

    /* ---------- أدوات ---------- */
    const esc = (s) => s == null ? '' : String(s)
        .replace(/&/g, '&amp;').replace(/</g, '&lt;')
        .replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');

    const $$ = (s) => document.querySelectorAll(s);
    const $  = (s) => document.querySelector(s);

    /* ---------- 1) الخدمات ---------- */
    function syncServices() {
        if (!Array.isArray(db.services) || !db.services.length) return;
        const grid = $('.services-grid');
        if (!grid) return;

        grid.innerHTML = db.services.map((s, i) => `
            <div class="service-card" data-aos="fade-up" data-aos-delay="${(i % 3) * 100}">
                <div class="card-icon"><i class="${esc(s.icon)}"></i></div>
                <h3>${esc(s.title)}</h3>
                <p>${esc(s.desc)}</p>
            </div>
        `).join('');
    }

    /* ---------- 2) الإحصاءات (العدادات) ---------- */
    function syncStats() {
        if (!Array.isArray(db.stats) || !db.stats.length) return;
        const grid = $('.stats-grid');
        if (!grid) return;

        grid.innerHTML = db.stats.map((s, i) => `
            <div class="stat-item" data-aos="zoom-in" data-aos-delay="${i * 200}">
                <h3 class="counter" data-target="${Number(s.value) || 0}">0</h3>
                <p>${esc(s.label)}</p>
            </div>
        `).join('');
    }

    /* ---------- 3) آراء العملاء ---------- */
    function syncTestimonials() {
        if (!Array.isArray(db.testimonials) || !db.testimonials.length) return;
        const grid = $('.testi-grid');
        if (!grid) return;

        grid.innerHTML = db.testimonials.map((t, i) => {
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

    /* ---------- 4) المعرض (Bento Grid) ---------- */
    function syncGallery() {
        if (!Array.isArray(db.gallery) || !db.gallery.length) return;
        const grid = $('.bento-grid');
        if (!grid) return;

        const aosCycle = ['zoom-in', 'fade-right', 'fade-up', 'fade-left'];

        grid.innerHTML = db.gallery.map((item, i) => {
            const sizeClass = item.size === 'wide' ? ' item-wide'
                            : item.size === 'tall' ? ' item-tall' : '';
            const aos = aosCycle[i % aosCycle.length];

            if (item.type === 'video') {
                const isAuto = i === 0;      // أول فيديو يشغّل تلقائياً
                const icon   = (item.label || '').includes('لمسات') ? 'fa-magic' : 'fa-play-circle';
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
    }

    /* ---------- 5) بيانات التواصل (هاتف / واتساب / ساعات) ---------- */
    function syncContact() {
        const s = db.settings || {};

        if (s.phone) {
            // كل روابط tel
            $$('a[href^="tel:"]').forEach(a => {
                const href = a.getAttribute('href') || '';
                const intl = href.includes('00966') || href.includes('+966');
                a.setAttribute('href', 'tel:' + (intl ? '00966' + s.phone.replace(/^0/, '') : s.phone));
            });

            // رقم الهاتف داخل الفوتر
            $$('.footer-col p').forEach(p => {
                const i = p.querySelector('i.fa-phone');
                if (i) p.innerHTML = `<i class="fas fa-phone"></i> ${esc(s.phone)}`;
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

    /* ---------- 6) إعادة تفعيل العدادات ---------- */
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

    /* ---------- 7) إعادة ربط فيديوهات الـ hover ---------- */
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

    /* ---------- التنفيذ ---------- */
    function run() {
        try {
            syncServices();
            syncStats();
            syncTestimonials();
            syncGallery();
            syncContact();

            // تحديث AOS ليأخذ العناصر الجديدة
            if (window.AOS) {
                if (typeof window.AOS.refreshHard === 'function') window.AOS.refreshHard();
                else if (typeof window.AOS.refresh === 'function') window.AOS.refresh();
            }

            rebindCounters();
            rebindHoverVideos();
        } catch (e) {
            console.warn('Sync: خطأ أثناء المزامنة', e);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => setTimeout(run, 120));
    } else {
        setTimeout(run, 120);
    }
})();