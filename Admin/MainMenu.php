<style>
	dialog::backdrop { 
		background: hsl(0, 0%, 0%, 0.3) !important;
		backdrop-filter: blur(5px) !important;
	}
	#MainMenu {
		overflow: auto;
		-ms-overflow-style: none;  /* IE and Edge */
		scrollbar-width: none;  /* Firefox */
	}
	#MainMenu::-webkit-scrollbar { display: none; }
@media screen and (min-width: 600px) {
	#MainMenu { overflow: hidden; }
}
</style>

<dialog id="MainMenu" class="w3-transparent w3-animate-zoom" style="border-color:transparent; width:90vw;">
<? require "Home.php"; $menuDialog = false; ?>
</dialog>

<script>
	const MAINMENU = document.querySelector (`dialog#MainMenu`);
    var mainNavOpen = false;
    function menu() {
		if (event.button == 2) return;
    	if (mainNavOpen) {
			MAINMENU.close();
    		mainNavOpen = false;
    	}else {
			MAINMENU.showModal();
    		setTimeout (() => mainNavOpen = true, 200);
    	}
    }
	window.onhashchange = () => { if (mainNavOpen) menu(); };
	$(document).keydown (e => {
		if (e.key == `Escape`) {
			mainNavOpen = true;
			menu();
		}
		if (['`', `AltGraph`].includes (e.key)) menu();
	});
	MAINMENU.onmousedown = e => {
		const dialogDimensions = MAINMENU.getBoundingClientRect()
		if (e.clientX < dialogDimensions.left || e.clientX > dialogDimensions.right || e.clientY < dialogDimensions.top || e.clientY > dialogDimensions.bottom) menu();
	};
</script>

