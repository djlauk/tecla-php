// helper script to generate a color scale over multiple steps

function pad(i) {
	return i < 10 ? "0" + i : i;
}

function printScale(
	className,
	hue,
	saturation,
	lightnessStart,
	lightnessEnd,
	numSteps
) {
	const delta = (lightnessEnd - lightnessStart) / (numSteps - 1);
	let lightness = lightnessStart;
	for (let i = 0; i < numSteps; i++) {
		console.log(
			`.${className}-${pad(
				i
			)} { background-color: hsl(${hue}, ${saturation}%, ${lightness.toFixed(
				2
			)}%); }`
		);
		lightness += delta;
	}
}

printScale("available", "var(--tecla-hue-available)", 100, 90, 20, 24);
printScale("taken", "var(--tecla-hue-taken)", 100, 90, 20, 24);
