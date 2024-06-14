const horizontalScroll = () => {
    const triggers = document.querySelectorAll("[data-animation='horizontal-scroll']");
console.log("triggers");
    triggers.forEach((trigger) => {
        const horizontalScrollContainer = trigger.querySelector("[data-child='horizontal-scroll']");
        const innerElements = horizontalScrollContainer.querySelectorAll(".block-card-block");

        const totalScrollDistance = horizontalScrollContainer.scrollWidth - horizontalScrollContainer.offsetWidth;

        gsap.to(innerElements, {
            x: window.innerWidth - horizontalScrollContainer.scrollWidth - horizontalScrollContainer.offsetWidth,
            scrollTrigger: {
                trigger: trigger,
                start: "top 30%",
                end: () => "+=" + totalScrollDistance, // Trigger at the end of the scroll container
                scrub: true,
                pin: true,
                pinSpacing: true, // Enable pinSpacing
            }
        });
    });
};

document.addEventListener("DOMContentLoaded", horizontalScroll);
