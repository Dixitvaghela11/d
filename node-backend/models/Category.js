npm init -y
npm install express mongoose body-parser cors bcrypt jsonwebtoken multer
const mongoose = require('mongoose');

const CategorySchema = new mongoose.Schema({
    name: { type: String, required: true, unique: true },
    details: { type: String, required: true }
});

module.exports = mongoose.model('Category', CategorySchema);
