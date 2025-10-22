import Plyr from 'plyr'
const podcastAudioInit = () => {
    const audioElements = [...document.getElementsByTagName('audio')]
    console.log('audioElements', audioElements)
    audioElements.forEach((audioEl) => {
        const player = new Plyr(audioEl, {})
    })
}
window.addEventListener('DOMContentLoaded', podcastAudioInit)