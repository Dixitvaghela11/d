const express = require('express');
const mongoose = require('mongoose');
const bodyParser = require('body-parser');
const cors = require('cors');
const bcrypt = require('bcrypt');
const jwt = require('jsonwebtoken');
const multer = require('multer');
const path = require('path');
const User = require('./models/User');
const Category = require('./models/Category');
const Product = require('./models/Product');

const app = express();
const PORT = 3000;
const JWT_SECRET = 'your_jwt_secret';

// Middleware
app.use(cors());
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: true }));

// MongoDB Connection
mongoose.connect('mongodb://localhost:27017/productDB', { useNewUrlParser: true, useUnifiedTopology: true })
    .then(() => console.log('MongoDB Connected'))
    .catch(err => console.log(err));

// Multer for file upload
const storage = multer.diskStorage({
    destination: function (req, file, cb) {
        cb(null, 'uploads/');
    },
    filename: function (req, file, cb) {
        cb(null, Date.now() + path.extname(file.originalname));
    }
});

const upload = multer({ storage: storage });

// Routes
app.post('/register', async (req, res) => {
    const { username, password } = req.body;
    const hashedPassword = await bcrypt.hash(password, 10);
    const user = new User({ username, password: hashedPassword });
    await user.save();
    res.json({ message: 'User registered' });
});

app.post('/login', async (req, res) => {
    const { username, password } = req.body;
    const user = await User.findOne({ username });
    if (!user) return res.status(400).json({ message: 'User not found' });
    const validPassword = await bcrypt.compare(password, user.password);
    if (!validPassword) return res.status(400).json({ message: 'Invalid password' });
    const token = jwt.sign({ _id: user._id }, JWT_SECRET);
    res.json({ token });
});

app.get('/categories', async (req, res) => {
    const categories = await Category.find();
    res.json(categories);
});

app.post('/categories', async (req, res) => {
    const { name, details } = req.body;
    const category = new Category({ name, details });
    await category.save();
    res.json(category);
});

app.get('/products', async (req, res) => {
    const products = await Product.find().populate('category');
    res.json(products);
});

app.post('/products', upload.single('image'), async (req, res) => {
    const { name, dob, category } = req.body;
    const image = req.file ? req.file.path : '';

    const product = new Product({
        name,
        dob,
        image,
        category
    });

    await product.save();
    res.json(product);
});

app.put('/products/:id', upload.single('image'), async (req, res) => {
    const { name, dob, category } = req.body;
    const image = req.file ? req.file.path : req.body.image;

    const updatedProduct = await Product.findByIdAndUpdate(req.params.id, {
        name,
        dob,
        image,
        category
    }, { new: true });

    res.json(updatedProduct);
});

app.delete('/products/:id', async (req, res) => {
    await Product.findByIdAndDelete(req.params.id);
    res.json({ message: 'Product deleted' });
});

// Start Server
app.listen(PORT, () => {
    console.log(`Server running on port ${PORT}`);
});