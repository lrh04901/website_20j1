module.exports = {
  mode: "jit",//开启jit模式
  purge: ['./index.html', './src/**/*.{vue, js}'],//处理css文件,使jit匹配src文件夹下的vue和js文件
  darkMode: false, // or 'media' or 'class'
  theme: {
    extend: {},
  },
  variants: {
    extend: {},
  },
  plugins: [],
}
