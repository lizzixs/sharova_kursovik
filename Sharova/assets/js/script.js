// ========== БУРГЕР-МЕНЮ БЕЗ ЗАТЕМНЕНИЯ ==========
const burger = document.getElementById('burgerMenu');
const nav = document.getElementById('mainNav');

if (burger && nav) {
    function openMenu() {
        burger.classList.add('active');
        nav.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeMenu() {
        burger.classList.remove('active');
        nav.classList.remove('active');
        document.body.style.overflow = '';
    }

    burger.addEventListener('click', function(e) {
        e.stopPropagation();
        if (nav.classList.contains('active')) {
            closeMenu();
        } else {
            openMenu();
        }
    });

    // Закрытие меню при клике вне области меню и кнопки
    document.addEventListener('click', function(e) {
        if (nav.classList.contains('active') && 
            !nav.contains(e.target) && 
            !burger.contains(e.target)) {
            closeMenu();
        }
    });

    // Обработка кликов по ссылкам в меню (якоря)
    nav.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            closeMenu();

            if (href && href.includes('#')) {
                const parts = href.split('#');
                const page = parts[0];
                const anchor = parts[1];
                
                const path = window.location.pathname;
                const currentPage = path.substring(path.lastIndexOf('/') + 1) || 'index.php';
                
                if ((page === currentPage) || (page === '' && currentPage === 'index.php') || (page === 'index.php' && currentPage === 'index.php')) {
                    e.preventDefault();
                    setTimeout(() => {
                        const target = document.getElementById(anchor);
                        if (target) {
                            target.scrollIntoView({ behavior: 'smooth' });
                        } else {
                            window.location.href = href;
                        }
                    }, 50);
                }
            }
        });
    });

    window.addEventListener('resize', function() {
        if (window.innerWidth > 768 && nav.classList.contains('active')) {
            closeMenu();
        }
    });
}

// ========== МОДАЛЬНОЕ ОКНО ДЛЯ УСЛУГ ==========
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('serviceModal');
    const modalBody = document.getElementById('modal-body');
    const closeBtn = modal ? modal.querySelector('.close-modal') : null;

    // Данные об услугах (можно дополнить или загружать с сервера)
    const serviceData = {
        auction: {
            title: 'Доступ к закрытым аукционам',
            description: 'Мы предоставляем прямой доступ к ведущим японским аукционам: USS Tokyo, TAA, JU Gifu. Проводим инспекцию лотов, помогаем с переводом документации и участвуем в торгах от вашего имени.',
            price: 'от 25 000 ₽',
            action: 'Оставить заявку'
        },
        logistics: {
            title: 'Доставка «под ключ»',
            description: 'Организуем полный цикл доставки: морской или авиафрахт, таможенное оформление, сертификация СБКТС, доставка автовозом до вашего города.',
            price: 'от 90 000 ₽',
            action: 'Рассчитать доставку'
        },
        restoration: {
            title: 'Реставрация и обслуживание',
            description: 'Профессиональная реставрация кузова, подбор оригинальных запчастей, детейлинг, техническое обслуживание японских автомобилей.',
            price: 'от 15 000 ₽',
            action: 'Записаться'
        },
        inspection: {
            title: 'Выездная инспекция',
            description: 'Наш эксперт лично осмотрит автомобиль перед покупкой, сделает детальные фото и видео, предоставит полный отчёт о состоянии.',
            price: 'от 5 000 ₽',
            action: 'Заказать инспекцию'
        },
        tuning: {
            title: 'Тюнинг и дооснащение',
            description: 'Установка тюнинг-комплектующих от ведущих брендов: HKS, BLITZ, RAYS, а также мультимедиа, сигнализации и дополнительное оборудование.',
            price: 'от 10 000 ₽',
            action: 'Обсудить проект'
        },
        finance: {
            title: 'Финансовые решения',
            description: 'Поможем оформить кредит или лизинг на специальных условиях, примем ваш автомобиль в Trade-In по выгодной цене.',
            price: '',
            action: 'Получить консультацию'
        }
    };

    // Обработчик клика по карточкам услуг
    document.querySelectorAll('.service-item[data-service]').forEach(item => {
        item.addEventListener('click', function() {
            const serviceKey = this.dataset.service;
            const data = serviceData[serviceKey];
            
            if (!data || !modal) return;

            // Формируем содержимое модального окна
            let html = `
                <h3>${data.title}</h3>
                <p>${data.description}</p>
            `;
            if (data.price) {
                html += `<div class="modal-price">${data.price}</div>`;
            }
            html += `
                <a href="request.php?type=${serviceKey}" class="btn-primary">
                    <i class="fas fa-paper-plane"></i> ${data.action}
                </a>
            `;
            
            modalBody.innerHTML = html;
            modal.style.display = 'block';
        });
    });

    // Закрытие модального окна
    if (closeBtn) {
        closeBtn.addEventListener('click', function() {
            modal.style.display = 'none';
        });
    }

    // Закрытие по клику вне содержимого
    window.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });

    // Закрытие по клавише Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal && modal.style.display === 'block') {
            modal.style.display = 'none';
        }
    });
});