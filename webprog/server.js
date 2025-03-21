const express = require("express");
const mysql = require("mysql2");
const cors = require("cors");

const app = express();
app.use(cors());
app.use(express.json());



const db = mysql.createConnection({
    host: "localhost", // Ensure MySQL is running on this host
    user: "root",
    password: "", // Add your MySQL password if applicable
    database: "db_attendancemanagement",
    port: 3306
});

db.connect((err) => {
    if (err) {
        console.error("Database connection failed:", err);
        return;
    }
    console.log("Connected to the database");
});

app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// API to GET tbl_approval data
// app.get("/api/approvals", (req, res) => {
//     const sql = "SELECT Employee_ID, approval_ID, approval_date, approval_remarks, request_type FROM tbl_approval";
//     db.query(sql, (err, results) => {
//         if (err) {
//             console.error("Error fetching data:", err);
//             res.status(500).json({ error: "Database error" });
//         } else {
//             res.json(results);
//         }
//     });
// });

app.listen(3306, () => {
    console.log("Server running on http://localhost:3306");
});