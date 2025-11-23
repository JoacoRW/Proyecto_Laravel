// Simple Node API wrapper for remote MySQL DB
// Usage: set env vars (see README) and run `node server.js` or use pm2/systemd
require('dotenv').config();
const express = require('express');
const mysql = require('mysql2');
const cors = require('cors');

const app = express();
app.use(cors());
app.use(express.json());

const db = mysql.createPool({
  host: process.env.NODE_DB_HOST || '127.0.0.1',
  port: process.env.NODE_DB_PORT ? parseInt(process.env.NODE_DB_PORT) : 3306,
  user: process.env.NODE_DB_USER || 'root',
  password: process.env.NODE_DB_PASS || '',
  database: process.env.NODE_DB_NAME || 'MediTrack',
  waitForConnections: true,
  connectionLimit: 10,
  queueLimit: 0
});

db.getConnection((err, connection) => {
  if (err) {
    console.error('Error conectando a MySQL:', err);
  } else {
    console.log('Conectado a MySQL (pool)');
    connection.release();
  }
});

// GET /api/pacientes
app.get('/api/pacientes', (req, res) => {
  const q = 'SELECT * FROM Paciente ORDER BY idPaciente DESC LIMIT 1000';
  db.query(q, (err, results) => {
    if (err) {
      console.error('MySQL error:', err);
      return res.status(500).json({ error: 'Error del servidor' });
    }
    res.json(results);
  });
});

// GET /api/pacientes/:id
app.get('/api/pacientes/:id', (req, res) => {
  const { id } = req.params;
  const q = 'SELECT * FROM Paciente WHERE idPaciente = ? LIMIT 1';
  db.query(q, [id], (err, results) => {
    if (err) {
      console.error('MySQL error:', err);
      return res.status(500).json({ error: 'Error del servidor' });
    }
    if (!results || results.length === 0) return res.status(404).json({ error: 'Paciente no encontrado' });
    res.json(results[0]);
  });
});

// POST /api/pacientes
app.post('/api/pacientes', (req, res) => {
  const body = req.body || {};
  const fields = [
    'nombrePaciente','fechaNacimiento','correo','telefono','direccion','sexo','nacionalidad','ocupacion','prevision','tipoSangre'
  ];
  const values = fields.map(f => body[f] || null);
  const placeholders = fields.map(() => '?').join(', ');
  const q = `INSERT INTO Paciente (${fields.join(',')}) VALUES (${placeholders})`;
  db.query(q, values, (err, results) => {
    if (err) {
      console.error('MySQL error:', err);
      return res.status(500).json({ error: 'Error creando paciente' });
    }
    res.json({ idPaciente: results.insertId, ...body, message: 'Paciente creado exitosamente' });
  });
});

const PORT = process.env.PORT ? parseInt(process.env.PORT) : 3001;
app.listen(PORT, () => console.log(`API Server running on http://0.0.0.0:${PORT}`));
