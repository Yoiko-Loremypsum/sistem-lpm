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
        umi: {
          50: '#F4F6FA',
          100: '#E6EFFA',
          200: '#C7DCF5',
          300: '#92BEED',
          400: '#4D96E1',
          500: '#1358B8', // Warna Primer Resmi UMI
          600: '#0E499D',
          700: '#0B3F8C', // Warna Primer Gelap (hover, tombol aktif)
          800: '#093270',
          900: '#062659',
          primary: '#1358B8',
          dark: '#0B3F8C',
          light: '#E6EFFA',
        },
      },
    },
  },
  plugins: [],
}
