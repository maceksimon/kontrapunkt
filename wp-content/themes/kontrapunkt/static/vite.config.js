import { defineConfig } from "vite";

export default defineConfig(({ mode = "production" }) => {
	const isProduction = mode === "production";

	return {
		base: "/dist/js/",
		build: {
			outDir: "dist/js",
			rollupOptions: {
				input: {
					script: "src/js/script.js",
				},
				output: {
					entryFileNames: "[name].js",
					chunkFileNames:
						"[name]." + (isProduction ? "[hash].min" : "dev") + ".js",
				},
			},
			minify: isProduction,
			sourcemap: !isProduction,
		},
	};
});
