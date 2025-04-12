const express = require('express');
const fs = require('fs').promises;
const path = require('path');
const cors = require('cors');
const { v4: uuidv4 } = require('uuid');
const xmlbuilder = require('xmlbuilder');

const app = express();
const port = 3000;

// Налаштування middleware
app.use(cors());
app.use(express.json());
app.use(express.static(path.join(__dirname, 'public')));
// Шляхи до файлів
const jsonFilePath = path.join(__dirname, 'records.json');
const xmlFilePath = path.join(__dirname, 'records.xml');

// Ініціалізація файлу JSON, якщо він не існує
async function initFiles() {
    try {
        await fs.access(jsonFilePath);
    } catch {
        await fs.writeFile(jsonFilePath, JSON.stringify([]));
    }
    try {
        await fs.access(xmlFilePath);
    } catch {
        const xml = xmlbuilder.create('records').end({ pretty: true });
        await fs.writeFile(xmlFilePath, xml);
    }
}
initFiles();

// Функція для збереження даних у JSON та XML
async function saveRecords(records) {
    // Збереження в JSON
    await fs.writeFile(jsonFilePath, JSON.stringify(records, null, 2));

    // Збереження в XML
    const xml = xmlbuilder.create('records');
    records.forEach(record => {
        xml.ele('record')
            .ele('id').txt(record.id).up()
            .ele('firstName').txt(record.firstName).up()
            .ele('lastName').txt(record.lastName).up()
            .ele('patronymic').txt(record.patronymic).up()
            .ele('previousStudy').txt(record.previousStudy).up()
            .ele('faculty').txt(record.faculty).up();
    });
    await fs.writeFile(xmlFilePath, xml.end({ pretty: true }));
}

// Отримання всіх записів
app.get('/api/records', async (req, res) => {
    try {
        const data = await fs.readFile(jsonFilePath, 'utf-8');
        const records = JSON.parse(data);
        res.json(records);
    } catch (error) {
        console.error('Помилка при зчитуванні записів:', error);
        res.status(500).json({ error: 'Не вдалося отримати записи' });
    }
});

// Створення нового запису
app.post('/api/applications', async (req, res) => {
    try {
        const newRecord = {
            id: uuidv4(),
            ...req.body,
        };

        const data = await fs.readFile(jsonFilePath, 'utf-8');
        const records = JSON.parse(data);
        records.push(newRecord);

        await saveRecords(records);
        res.json({ message: 'Запис створено', record: newRecord });
    } catch (error) {
        console.error('Помилка при створенні запису:', error);
        res.status(500).json({ error: 'Не вдалося створити запис' });
    }
});

// Оновлення запису
app.put('/api/records/:id', async (req, res) => {
    try {
        const { id } = req.params;
        const updatedData = req.body;

        const data = await fs.readFile(jsonFilePath, 'utf-8');
        let records = JSON.parse(data);

        const index = records.findIndex(record => record.id === id);
        if (index === -1) {
            return res.status(404).json({ error: 'Запис не знайдено' });
        }

        records[index] = { ...records[index], ...updatedData };
        await saveRecords(records);
        res.json({ message: 'Запис оновлено', record: records[index] });
    } catch (error) {
        console.error('Помилка при оновленні запису:', error);
        res.status(500).json({ error: 'Не вдалося оновити запис' });
    }
});

// Видалення запису
app.delete('/api/records/:id', async (req, res) => {
    try {
        const { id } = req.params;

        const data = await fs.readFile(jsonFilePath, 'utf-8');
        let records = JSON.parse(data);

        const filteredRecords = records.filter(record => record.id !== id);
        if (filteredRecords.length === records.length) {
            return res.status(404).json({ error: 'Запис не знайдено' });
        }

        await saveRecords(filteredRecords);
        res.json({ message: 'Запис видалено' });
    } catch (error) {
        console.error('Помилка при видаленні запису:', error);
        res.status(500).json({ error: 'Не вдалося видалити запис' });
    }
});

// Запуск сервера
app.listen(port, () => {
    console.log(`Сервер запущено на http://localhost:${port}`);
});