// theme-utils.js
const themeUtils = {
  isColorDark(hexColor) {
      const r = parseInt(hexColor.slice(1, 3), 16);
      const g = parseInt(hexColor.slice(3, 5), 16);
      const b = parseInt(hexColor.slice(5, 7), 16);
      const yiq = ((r * 299) + (g * 587) + (b * 114)) / 1000;
      return yiq < 128;
  },

  updateThemedElements() {
      const themedSections = document.querySelectorAll('.navbar-themed-section');
      themedSections.forEach(section => {
          const backgroundColor = getComputedStyle(section).backgroundColor;
          const hexColor = this.rgbToHex(backgroundColor);
          const themedTexts = section.querySelectorAll('.themed-text');
          
          const textColor = this.isColorDark(hexColor) ? '#FFFFFF' : '#000000';
          themedTexts.forEach(element => {
              element.style.color = textColor;
          });
      });
  },

  rgbToHex(rgb) {
      const rgbValues = rgb.match(/\d+/g);
      const r = parseInt(rgbValues[0]);
      const g = parseInt(rgbValues[1]);
      const b = parseInt(rgbValues[2]);
      
      return '#' + [r, g, b].map(x => {
          const hex = x.toString(16);
          return hex.length === 1 ? '0' + hex : hex;
      }).join('');
  }
};

document.addEventListener('DOMContentLoaded', () => {
  themeUtils.updateThemedElements();
});