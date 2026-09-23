function initRevealAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('revealed');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    document.querySelectorAll('.reveal:not(.revealed)').forEach(el => observer.observe(el));

    document.querySelectorAll('.stagger-children').forEach(parent => {
        const children = parent.children;
        Array.from(children).forEach((child, index) => {
            child.style.animationDelay = `${index * 0.1}s`;
        });
    });
}

document.addEventListener('DOMContentLoaded', initRevealAnimations);

document.addEventListener('livewire:navigated', initRevealAnimations);
