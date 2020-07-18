class Slide{
    constructor(blockSlider, boutonLeft, boutonRight, nbItemBySlide, url = null, ajaxFunction = null)
    {
        this.blockSlider = blockSlider;
        this.boutonLeft = boutonLeft;
        this.boutonRight = boutonRight;
        this.nbSlide = 1;
        this.nbItemBySlide = nbItemBySlide;
        this.positionSlider = 1;
        this.url = url;
        this.ajaxFunction = ajaxFunction;
    }
    toLeft()
    {
        if (this.positionSlider > 1) {
            this.positionSlider -= 1;
            this.blockSlider.style.left = '-' + ((this.positionSlider - 1) * 100) + '%';
        } else {this.goToLastSlide();}
    }
    toRight()
    {
        if (this.positionSlider === this.nbSlide) {
            this.ajaxFunction(this, this.url);
        } else {
            this.positionSlider += 1;
            this.blockSlider.style.left = '-' + ((this.positionSlider - 1) * 100) + '%';
        }
    }
    goToFirstSlide()
    {
        if (this.positionSlider !== 1) {
            this.positionSlider = 1;
            this.blockSlider.style.left = '-' + ((this.positionSlider - 1) * 100) + '%';
        }
    }
    goToLastSlide()
    {
        this.positionSlider = this.nbSlide;
        this.blockSlider.style.left = '-' + ((this.positionSlider - 1) * 100) + '%';
    }
    init()
    {
        this.boutonLeft.addEventListener("click", this.toLeft.bind(this));
        this.boutonRight.addEventListener("click", this.toRight.bind(this));
    }
}