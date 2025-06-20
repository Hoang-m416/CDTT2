function updateQuantity(change) {
    const input = document.getElementById("quantity");
    let current = parseInt(input.value);
    const min = parseInt(input.min);
    const max = parseInt(input.max);

    current += change;
    if (current < min) current = min;
    if (current > max) current = max;

    input.value = current;
}