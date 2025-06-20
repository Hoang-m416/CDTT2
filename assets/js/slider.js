function scrollSlider(direction) {
    const slider = document.getElementById("other");
    const scrollAmount = 220;
    slider.scrollBy({
        left: direction * scrollAmount,
        behavior: "smooth"
    });
}