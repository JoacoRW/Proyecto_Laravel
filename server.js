// Simple Node API wrapper for remote MySQL DB
// Usage: set env vars (see README) and run `node server.js` or use pm2/systemd
import dotenv from 'dotenv';
dotenv.config();
import express from 'express';
import mysql from 'mysql2';
import cors from 'cors';

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

// SSE clients
const sseClients = [];

function sendSseEvent(event, data) {
  const payload = `event: ${event}\n` + `data: ${JSON.stringify(data)}\n\n`;
  console.log(`SSE broadcast: event=${event} clients=${sseClients.length}`);
  sseClients.forEach((res, idx) => {
    try {
      res.write(payload);
      // optional per-client log
      // console.log(` -> sent to client #${idx}`);
    } catch (e) {
      console.error('SSE write error to client', idx, e);
    }
  });
}

// keep-alive pings for SSE connections
setInterval(() => {
  sseClients.forEach((res) => {
    try { res.write(': ping\n\n'); } catch (e) { /* ignore */ }
  });
}, 20000);

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

  // Determine which columns actually exist on the Paciente table and only insert those
  db.query("SHOW COLUMNS FROM Paciente", (err, columns) => {
    if (err) {
      console.error('Error fetching columns for Paciente:', err);
      return res.status(500).json({ error: 'Error interno', details: err.message });
    }

    const existingCols = columns.map(c => c.Field);
    const preferredFields = [
      'nombrePaciente','fechaNacimiento','correo','telefono','direccion','sexo','nacionalidad','ocupacion','prevision','tipoSangre'
    ];

    const fields = preferredFields.filter(f => existingCols.includes(f));
    if (fields.length === 0) {
      // nothing to insert besides maybe timestamps; insert a minimal row
      const q = "INSERT INTO Paciente (nombrePaciente) VALUES (?)";
      const values = [ body.nombrePaciente || null ];
      return db.query(q, values, (err2, results2) => {
        if (err2) {
          console.error('MySQL error inserting minimal paciente:', err2);
          console.error('Request body:', body);
          return res.status(500).json({ error: 'Error creando paciente', details: err2.message });
        }
        const created = { idPaciente: results2.insertId, ...body };
        try { sendSseEvent('paciente_created', created); } catch (e) { console.error('SSE broadcast error', e); }
        return res.json({ ...created, message: 'Paciente creado exitosamente' });
      });
    }

    const values = fields.map(f => (body[f] !== undefined ? body[f] : null));
    const placeholders = fields.map(() => '?').join(', ');
    const q = `INSERT INTO Paciente (${fields.join(',')}) VALUES (${placeholders})`;

    db.query(q, values, (err2, results) => {
      if (err2) {
        console.error('MySQL error inserting paciente:', err2);
        console.error('Request body:', body);
        return res.status(500).json({ error: 'Error creando paciente', details: err2.message });
      }
      const created = { idPaciente: results.insertId, ...body };
      try { sendSseEvent('paciente_created', created); } catch (e) { console.error('SSE broadcast error', e); }
      res.json({ ...created, message: 'Paciente creado exitosamente' });
    });
  });
});

// Generic broadcast endpoint (useful for other services to notify SSE clients)
app.post('/api/broadcast', (req, res) => {
  const { event, data } = req.body || {};
  if (!event) return res.status(400).json({ error: 'Missing event name' });
  try {
    sendSseEvent(event, data || {});
    return res.json({ ok: true });
  } catch (e) {
    console.error('Broadcast error', e);
    return res.status(500).json({ error: 'Broadcast failed' });
  }
});

// SSE endpoint
app.get('/events', (req, res) => {
  // Headers for SSE
  res.setHeader('Content-Type', 'text/event-stream');
  res.setHeader('Cache-Control', 'no-cache');
  res.setHeader('Connection', 'keep-alive');
  res.flushHeaders && res.flushHeaders();

  // initial retry value
  res.write('retry: 10000\n\n');

  sseClients.push(res);
  console.log('SSE client connected, total clients =', sseClients.length);

  req.on('close', () => {
    const i = sseClients.indexOf(res);
    if (i !== -1) sseClients.splice(i, 1);
    console.log('SSE client disconnected, total clients =', sseClients.length);
  });
});

const PORT = process.env.PORT ? parseInt(process.env.PORT) : 3001;
app.listen(PORT, () => console.log(`API Server running on http://0.0.0.0:${PORT}`));
