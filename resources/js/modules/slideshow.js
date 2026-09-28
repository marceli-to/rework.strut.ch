// Homepage highlight slideshow (legacy swiper.js): cross-fade every 4.5s,
// 1.5s fade, endless. A video slide stops the autoplay, plays from the start
// and hands back to the autoplay when it has ended.

import Swiper from 'swiper';
import { Autoplay, EffectFade } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/effect-fade';

export function initSlideshow(root = document) {
	const el = root.querySelector('[data-slideshow] .swiper');
	if (!el) return;

	const swiper = new Swiper(el, {
		modules: [Autoplay, EffectFade],
		effect: 'fade',
		fadeEffect: { crossFade: true },
		autoplay: { delay: 4500, disableOnInteraction: false },
		loop: true,
		speed: 1500,
		autoHeight: false,
	});

	swiper.on('slideChangeTransitionEnd', () => {
		const video = swiper.slides[swiper.activeIndex]?.querySelector('video');
		if (!video) return;

		swiper.autoplay.stop();
		video.currentTime = 0;
		video.play();
		video.addEventListener('ended', () => swiper.autoplay.start(), { once: true });
	});
}
