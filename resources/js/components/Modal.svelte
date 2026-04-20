<script>
	export let showModal; // boolean
	export let classes = "";
	let dialog; // HTMLDialogElement



	$: if (dialog && showModal) dialog.showModal();
</script>

<!-- svelte-ignore a11y-click-events-have-key-events a11y-no-noninteractive-element-interactions -->
{#if showModal}

<dialog
	bind:this={dialog}
	on:close={() => (showModal = false)}
	on:click|self={() => dialog.close()}
	class={classes} 
>
	<!-- svelte-ignore a11y-no-static-element-interactions -->
	<div on:click|stopPropagation>
		<slot name="header" />
		<button class="absolute  right-4 top-4"  on:click={() => dialog.close()}><iconify-icon icon="line-md:close" width="24" height="24"></iconify-icon></button>
		<hr  class="mt-3"/>
		<slot />
		<hr class="my-4"/>
		<!-- svelte-ignore a11y-autofocus -->
		<div class="flex justify-end gap-12">
			

			<slot name="btn_footer">
	
			</slot>
		</div>
	</div>
</dialog>
{/if}

<style>
	dialog {
		max-width: 98vw;
		border: 4px solid black;
		padding: 0;
	}
	dialog::backdrop {
		background: rgba(0, 0, 0, 0.3);
		backdrop-filter: blur(0.5px);
	}
	dialog > div {
		padding: 1em;
	}
	dialog[open] {
		animation: zoom 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
	}
	@keyframes zoom {
		from {
			transform: scale(0.95);
		}
		to {
			transform: scale(1);
		}
	}
	dialog[open]::backdrop {
		animation: fade 0.2s ease-out;
	}
	@keyframes fade {
		from {
			opacity: 0;
		}
		to {
			opacity: 1;
		}
	}
	button {
		display: block;
	}
    hr {
        opacity: .2;
    }
</style>
