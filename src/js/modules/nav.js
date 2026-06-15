/**
 * Active le menu burger mobile avec gestion du clavier et du focus.
 */
export function initNavigation() {
	const nav = document.querySelector(".site-nav");

	if (!nav) {
		return;
	}

	const toggle = nav.querySelector(".site-nav__toggle");
	const menu = nav.querySelector(".site-nav__list");

	if (!toggle || !menu) {
		return;
	}

	const desktop = window.matchMedia("(min-width: 48rem)");

	const closeMenu = ({ restoreFocus = false } = {}) => {
		nav.classList.remove("site-nav--open");
		toggle.setAttribute("aria-expanded", "false");

		if (restoreFocus) {
			toggle.focus();
		}
	};

	const openMenu = () => {
		nav.classList.add("site-nav--open");
		toggle.setAttribute("aria-expanded", "true");
		menu.querySelector("a")?.focus();
	};

	nav.classList.add("site-nav--enhanced");

	toggle.addEventListener("click", () => {
		if (nav.classList.contains("site-nav--open")) {
			closeMenu();
			return;
		}

		openMenu();
	});

	document.addEventListener("click", (event) => {
		if (!nav.contains(event.target)) {
			closeMenu();
		}
	});

	nav.addEventListener("keydown", (event) => {
		if (event.key === "Escape" && nav.classList.contains("site-nav--open")) {
			closeMenu({ restoreFocus: true });
		}
	});

	desktop.addEventListener("change", () => closeMenu());
}
