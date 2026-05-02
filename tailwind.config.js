/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './resources/views/**/*.blade.php',
    './resources/js/**/*.js',
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
      },
      colors: {
        accent: {
          50:  '#eef2ff',
          100: '#e0e7ff',
          200: '#c7d2fe',
          400: '#818cf8',
          500: '#6366f1',
          600: '#4f46e5',
          700: '#4338ca',
        },
      },
      fontSize: {
        '2xs': ['11px', { lineHeight: '1.4' }],
        xs:    ['13px', { lineHeight: '1.5' }],
        sm:    ['14px', { lineHeight: '1.6' }],
        base:  ['15px', { lineHeight: '1.6' }],
        lg:    ['18px', { lineHeight: '1.4' }],
        xl:    ['22px', { lineHeight: '1.3' }],
        '2xl': ['28px', { lineHeight: '1.2' }],
        '3xl': ['36px', { lineHeight: '1.15' }],
        '4xl': ['40px', { lineHeight: '1.1' }],
      },
      borderRadius: {
        DEFAULT: '6px',
        sm:  '4px',
        md:  '6px',
        lg:  '10px',
        xl:  '14px',
        '2xl': '20px',
      },
      boxShadow: {
        none: 'none',
        ring: '0 0 0 3px rgba(99,102,241,0.18)',
        'ring-danger': '0 0 0 3px rgba(220,38,38,0.15)',
      },
      spacing: {
        '4.5': '18px',
        '5.5': '22px',
        '13': '52px',
        '15': '60px',
        '18': '72px',
        '22': '88px',
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms')({ strategy: 'class' }),
    require('@tailwindcss/typography'),
  ],
}
