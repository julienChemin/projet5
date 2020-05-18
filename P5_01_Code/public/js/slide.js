class Slide{
	constructor(blockSlides, boutonLeft, boutonRight, nbItemBySlide, ajaxFunction = null){
		this.blockSlides = blockSlides;
		this.boutonLeft = boutonLeft;
		this.boutonRight = boutonRight;
		this.nbSlide = 1;
		this.nbItemBySlide = nbItemBySlide;
		this.positionSlider = 1;
		this.ajaxFunction = ajaxFunction;
	}
	toLeft(){
		if (this.positionSlider > 1) {
			this.positionSlider -= 1;
			this.blockSlides.style.left = '-' + ((this.positionSlider - 1) * 100) + '%';
		}
	}
	toRight(){
		if (this.positionSlider === this.nbSlide) {
			this.ajaxFunction(this);
		} else {
			this.positionSlider += 1;
			this.blockSlides.style.left = '-' + ((this.positionSlider - 1) * 100) + '%';
		}
	}
	init(){
		this.boutonLeft.addEventListener("click", this.toLeft.bind(this));
		this.boutonRight.addEventListener("click", this.toRight.bind(this));
	}
}