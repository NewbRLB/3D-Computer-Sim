// 1. Scene, Camera, and Renderer Setup
const scene = new THREE.Scene();
scene.background = new THREE.Color(0x0b0f19);

const camera = new THREE.PerspectiveCamera(60, window.innerWidth / window.innerHeight, 0.1, 1000);
camera.position.set(6, 6, 8);

const renderer = new THREE.WebGLRenderer({ antialias: true });
renderer.setSize(window.innerWidth, window.innerHeight);
renderer.shadowMap.enabled = true;

// Append renderer specifically to our container
document.getElementById('canvas-container').appendChild(renderer.domElement);

// 2. Camera Controls
let controls;
if (typeof THREE.OrbitControls !== 'undefined') {
    controls = new THREE.OrbitControls(camera, renderer.domElement);
    controls.enableDamping = true;
    controls.dampingFactor = 0.05;
} else {
    console.warn("OrbitControls.js is not loaded yet.");
}

// 3. Lighting Setup
const ambientLight = new THREE.AmbientLight(0xffffff, 1.2);
scene.add(ambientLight);

const dirLight = new THREE.DirectionalLight(0xffffff, 1.8);
dirLight.position.set(10, 25, 15);
dirLight.castShadow = true;
scene.add(dirLight);

const fillLight = new THREE.DirectionalLight(0x90b0ff, 0.8);
fillLight.position.set(-10, 15, -10);
scene.add(fillLight);

// 4. Build Environment / Base Plate
const floorGeo = new THREE.BoxGeometry(9.5, 0.2, 9.5);
const floorMat = new THREE.MeshStandardMaterial({ color: 0x1a2130, roughness: 0.4 });
const chassisFloor = new THREE.Mesh(floorGeo, floorMat);
chassisFloor.receiveShadow = true;
scene.add(chassisFloor);

// 5. Motherboard Tray Placeholder
const mbGeo = new THREE.BoxGeometry(5.5, 0.1, 5.5);
const mbMat = new THREE.MeshStandardMaterial({ color: 0x113322, roughness: 0.3 });
const motherboard = new THREE.Mesh(mbGeo, mbMat);
motherboard.position.set(0, 0.15, 0);
motherboard.castShadow = true;
motherboard.receiveShadow = true;
scene.add(motherboard);

// Grid Helper
const gridHelper = new THREE.GridHelper(12, 12, 0x00ffcc, 0x222222);
gridHelper.position.y = -0.01;
scene.add(gridHelper);

// Window resize handler
window.addEventListener('resize', () => {
    camera.aspect = window.innerWidth / window.innerHeight;
    camera.updateProjectionMatrix();
    renderer.setSize(window.innerWidth, window.innerHeight);
});

// 6. Animation Loop
function animate() {
    requestAnimationFrame(animate);
    if (controls) controls.update();
    renderer.render(scene, camera);
}
animate();