
import * as THREE from 'https://cdn.jsdelivr.net/npm/three@0.125.2/build/three.module.js';

/*
* Scene
*/
const scene = new THREE.Scene()
scene.background = new THREE.Color( 0xffffff )

/*
* Camera
*/
const resolution = window.innerWidth / window.innerHeight
const camera = new THREE.PerspectiveCamera(45, resolution, 0.5, 1000)
camera.position.setZ(20)

/*
* Renderer
*/
const renderer =  new THREE.WebGLRenderer({
    canvas: document.querySelector('#app')
})
renderer.setPixelRatio(window.devicePixelRatio)
renderer.setSize(window.innerWidth * 0.8, window.innerHeight * 0.8);

/*
* Lighting
*/
const pointLight = new THREE.PointLight(0xDCDCDC)
pointLight.position.set(5, -28.5,5)

const pointLight2 = new THREE.PointLight(0xDCDCDC)
pointLight2.position.set(5, 5, 5)

const ambientLight = new THREE.AmbientLight( 0x404040 )
const directionalLight = new THREE.DirectionalLight( 0xffffff, 0.5 )

scene.add(pointLight,pointLight2, ambientLight, directionalLight)

/*
* Coin
*/
const textureLoader = new THREE.TextureLoader()

// Face 1
const face1Texture = textureLoader.load('images/coinside.png')
const face1Material = new THREE.MeshLambertMaterial({map:face1Texture})

// Face 2
const face2Texture = textureLoader.load('images/coinside.png')
const face2Material = new THREE.MeshLambertMaterial({map:face2Texture})

// Edge
const whiteMaterial = new THREE.MeshStandardMaterial({color: 0xFFD700})

const coinMaterials = [
    whiteMaterial, // Coin edge
    face1Material, // Coin face 1
    face2Material, // Coin face 2
]

const coinGeometry = new THREE.CylinderGeometry(3, 3, 0.5, 20, 20, false)
const coin = new THREE.Mesh( coinGeometry, coinMaterials )
scene.add( coin )

function animate () {
    requestAnimationFrame(animate)

    coin.rotation.y += 0.01
    coin.rotation.x += 0.03
    coin.rotation.z += 0.01

    renderer.render(scene, camera)
}

animate()
