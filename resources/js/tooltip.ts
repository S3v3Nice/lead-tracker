type tooltipOptions = {
    position: 'top' | 'bottom' | 'left' | 'right';
    text: string;
}

export function tooltipDirective(element, binding) {
    element.setAttribute("data-tooltip", binding.value?.text || binding.value);
    element.classList.add("tooltip");

    const position = binding.value.position || getPositionClass(binding.modifiers);
    element.classList.add(`tooltip-${position}`);
}

function getPositionClass(modifiers) {
    if (modifiers.top) {
        return 'top';
    } else if (modifiers.bottom) {
        return 'bottom';
    } else if (modifiers.left) {
        return 'left';
    } else if (modifiers.right) {
        return 'right';
    }
    return 'top';
}
