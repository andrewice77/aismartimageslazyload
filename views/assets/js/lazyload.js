document.addEventListener("DOMContentLoaded", function () {

    if( typeof aismartimageslazyload == 'undefined' )
        return false;

    const excludedClasses = (aismartimageslazyload.excluded || "").split(",");
    const placeholder = aismartimageslazyload.placeholder && aismartimageslazyload.placeholder.trim() !== ''
        ? aismartimageslazyload.placeholder
        : 'data:image/svg+xml;base64,' + btoa('<svg xmlns="http://www.w3.org/2000/svg" width="10" height="7"><rect width="100%" height="100%" fill="#eee"/></svg>');


    const isExcluded = (el) => {
        return excludedClasses.some(cls => el.classList.contains(cls.replace('.', '').trim()));
    };
    const imgs = document.querySelectorAll('img:not([data-smartlazy])');

    imgs.forEach(img => {
        if (isExcluded(img)) return;

        if (!img.dataset.src && img.src) {
            img.dataset.src = img.src;
        }

        img.src = placeholder;
        img.setAttribute('data-smartlazy', '1');
        img.classList.add('lazy-img');
    });

    const lazyItems = document.querySelectorAll('[data-smartlazy], [data-smartlazy-bg]');

    const lazyLoad = (el) => {
        if (el.dataset.src) {
            el.src = el.dataset.src;
            el.removeAttribute('data-smartlazy');
        }
        if (el.dataset.smartlazyBg) {
            el.style.backgroundImage = `url('${el.dataset.smartlazyBg}')`;
            el.removeAttribute('data-smartlazy-bg');
        }
    };

    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries, obs) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    lazyLoad(entry.target);
                    obs.unobserve(entry.target);
                }
            });
        });

        lazyItems.forEach(el => observer.observe(el));
    } else {
        lazyItems.forEach(el => lazyLoad(el));
    }
});
