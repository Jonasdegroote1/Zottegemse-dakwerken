const horizontalScroll = () => {
    const triggers = document.querySelectorAll("[data-animation='horizontal-scroll']");

    triggers.forEach((trigger) => {
        const horizontalScrollContainer = trigger.querySelector("[data-child='horizontal-scroll']");
        const innerElements = horizontalScrollContainer.querySelectorAll(".block-card-block");

        gsap.to(innerElements, {
            x: window.innerWidth - horizontalScrollContainer.scrollWidth - horizontalScrollContainer.offsetWidth,
            scrollTrigger: {
                trigger: trigger,
                start: "top 30%",
                end: "bottom top",
                scrub: true,
                pin: true,
                pinSpacing: true, // Enable pinSpacing
                onUpdate: (self) => {
                    const sectionWidth = horizontalScrollContainer.offsetWidth;
                    const containerWidth = window.innerWidth;
                    const padding = (containerWidth - sectionWidth) / 2;

                    // Center the pinned section
                    horizontalScrollContainer.style.marginLeft = padding + "px";
                    horizontalScrollContainer.style.marginRight = padding + "px";
                }
            }
        });
    });
};

document.addEventListener("DOMContentLoaded", horizontalScroll);
