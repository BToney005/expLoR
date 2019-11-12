const { DeckEncoder } = require('runeterra')
// const deck = DeckEncoder.decode('CEAAECABAQJRWHBIFU2DOOYIAEBAMCIMCINCILJZAICACBANE4VCYBABAILR2HRL')
const deck = DeckEncoder.decode(process.argv[2]);
console.log(JSON.stringify(deck));