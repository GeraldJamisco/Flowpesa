// js/idv.js
(function(){
  const constraints = (facingMode) => ({
    audio: false,
    video: { facingMode, width: { ideal: 1280 }, height: { ideal: 720 } }
  });

  async function getStream(prefer = 'environment') {
    try { return await navigator.mediaDevices.getUserMedia(constraints(prefer)); }
    catch { try { return await navigator.mediaDevices.getUserMedia(constraints('user')); }
           catch { return null; } }
  }

  function captureToDataURL(video, maxW = 1280) {
    const ratio = video.videoWidth / video.videoHeight || (16/9);
    const w = Math.min(maxW, video.videoWidth || 1280);
    const h = Math.round(w / ratio);
    const canvas = document.getElementById('capture-canvas');
    canvas.width = w; canvas.height = h;
    const ctx = canvas.getContext('2d');
    ctx.drawImage(video, 0, 0, w, h);
    return canvas.toDataURL('image/jpeg', 0.9);
  }

  window.IDV = {
    async start(side){   // side = 'front' | 'back'
      const video = document.getElementById('idv-video');
      const fallback = document.getElementById('fallback');
      const fileInput = document.getElementById(`file-${side}`);
      const snapBtn = document.getElementById(`snap-${side}`);
      const nextBtn = document.getElementById(`next-${side}`);
      const rotateBtn = document.getElementById('rotate');

      let stream = await getStream('environment');
      if (stream) {
        video.srcObject = stream;
        nextBtn.disabled = true;
        fallback.hidden = true;
      } else {
        fallback.hidden = false;
      }

      // take photo
      snapBtn?.addEventListener('click', () => {
        if (!video.videoWidth) return;
        const data = captureToDataURL(video);
        sessionStorage.setItem(`flowpesa_idv_${side}`, data);
        nextBtn.disabled = false;
      });

      // pick from device
      fileInput?.addEventListener('change', () => {
        const file = fileInput.files?.[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = () => {
          sessionStorage.setItem(`flowpesa_idv_${side}`, reader.result);
          nextBtn.disabled = false;
        };
        reader.readAsDataURL(file);
      });

      // switch camera (if multiple)
      rotateBtn?.addEventListener('click', async () => {
        if (stream) stream.getTracks().forEach(t => t.stop());
        // naive toggle: try the other facing mode
        stream = await getStream('user');
        if (!stream) stream = await getStream('environment');
        if (stream) { video.srcObject = stream; fallback.hidden = true; }
      });

      // go to next step
      nextBtn?.addEventListener('click', () => {
        const url = (side === 'front') ? 'verify-id-back.html' : 'verify-id-review.html';
        location.href = url;
      });

      // stop tracks when navigating away
      window.addEventListener('beforeunload', () => stream?.getTracks().forEach(t => t.stop()));
    }
  };
})();
