/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        'viola-magia': '#7B2869',
        'fucsia-magia': '#E91E8C',
        'arancio-magia': '#FF6B35',
      },
      fontFamily: {
        'sans': ['Poppins', 'system-ui', 'sans-serif'],
      }
    },
  },
  plugins: [],
}
