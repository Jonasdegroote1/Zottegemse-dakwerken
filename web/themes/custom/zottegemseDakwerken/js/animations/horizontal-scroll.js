const horizontalScroll = () => {
    const triggers = document.querySelectorAll("[data-animation='horizontal-scroll']");

    triggers.forEach((trigger) => {
        const horizontalScrollContainer = trigger.querySelector("[data-child='horizontal-scroll']");
        const innerElements = horizontalScrollContainer.querySelectorAll(".block-card-block");

        gsap.to(innerElements, {
            x: window.innerWidth - horizontalScrollContainer.scrollWidth -horizontalScrollContainer.offsetWidth,
            scrollTrigger: {
                trigger: trigger,
                start: "top 10%",
                end: "bottom top",
                scrub: true,
                markers: true,
                pin : true,
            }
        });
    });
};

document.addEventListener("DOMContentLoaded", horizontalScroll);
