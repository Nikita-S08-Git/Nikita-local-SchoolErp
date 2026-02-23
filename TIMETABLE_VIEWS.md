# Timetable Views - Quick Reference

## 📋 Two View Options

Your timetable now has **TWO viewing options**:

### **1. Grid View (Weekly Calendar)**
**URL:** http://127.0.0.1:8000/academic/timetable

Shows timetable in a weekly grid format:
```
┌──────────┬─────────┬─────────┬───────────┐
│   Time   │ Monday  │ Tuesday │ Wednesday │
├──────────┼─────────┼─────────┼───────────┤
│ 09:00-10 │  Math   │ English │  Science  │
│          │ Teacher │ Teacher │  Teacher  │
│          │ Room    │ Room    │  Room     │
└──────────┴─────────┴─────────┴───────────┘
```

### **2. Table View (List Format)**
**URL:** http://127.0.0.1:8000/academic/timetable/table

Shows timetable in a table format:
```
| Module    | Lecturer  | Group | Day | Time  | Room   |
|-----------|-----------|-------|-----|-------|--------|
| Math      | Mr. John  | IT 1  | Mon | 8–10  | Lab 1  |
| English   | Ms. Sarah | IT 1  | Mon | 10–12 | Room 3 |
| Database  | Mr. Alex  | IT 2  | Tue | 1–3   | Lab 2  |
```

---

## 🔄 Switching Between Views

### **From Grid View → Table View:**
Click "📋 Table View" button at top

### **From Table View → Grid View:**
Click "📅 Grid View" button at top

---

## 🎯 Table View Features

### **Columns:**
1. **Module** - Subject name
2. **Lecturer** - Teacher name
3. **Group** - Division/Section name
4. **Day** - Day of week
5. **Time** - Start–End time (e.g., 8–10)
6. **Room** - Room number
7. **Actions** - Edit/Delete buttons

### **Filters:**
- **Division Filter** - Show specific division only
- **Day Filter** - Show specific day only
- **Pagination** - 20 entries per page

### **Actions:**
- ✏️ **Edit** - Modify schedule
- 🗑️ **Delete** - Remove schedule
- ➕ **Add Schedule** - Create new entry

---

## 📊 Sample Data

After seeding, you'll see entries like:

| Module | Lecturer | Group | Day | Time | Room |
|--------|----------|-------|-----|------|------|
| Mathematics | Mr. John Smith | Class 10-A | Monday | 09:00–10:00 | Room 101 |
| English | Ms. Sarah Jones | Class 10-A | Monday | 10:00–11:00 | Room 102 |
| Science | Dr. Mike Brown | Class 10-A | Monday | 11:00–12:00 | Room 103 |
| History | Ms. Lisa White | Class 10-A | Monday | 12:00–13:00 | Room 104 |
| Geography | Mr. Tom Davis | Class 10-A | Monday | 14:00–15:00 | Room 105 |

---

## 🚀 Quick Access

### **URLs:**
- **Grid View:** `/academic/timetable`
- **Table View:** `/academic/timetable/table`
- **Add Schedule:** `/academic/timetable/create`
- **Edit Schedule:** `/academic/timetable/{id}/edit`

### **Navigation:**
```
Timetable Management
├── Grid View (Weekly Calendar)
│   └── Shows: Time slots × Days grid
└── Table View (List Format)
    └── Shows: Module, Lecturer, Group, Day, Time, Room
```

---

## 🎨 Use Cases

### **Use Grid View When:**
- ✅ You want to see the whole week at once
- ✅ You need to check time conflicts
- ✅ You want a visual calendar layout
- ✅ You're planning the weekly schedule

### **Use Table View When:**
- ✅ You want to see all details in one place
- ✅ You need to filter by division or day
- ✅ You want to print a simple list
- ✅ You need to search specific entries
- ✅ You want to see lecturer assignments clearly

---

## 📝 Example Workflow

### **Scenario: View IT 1 Group's Monday Schedule**

**Using Table View:**
1. Go to `/academic/timetable/table`
2. Select "IT 1" from Division dropdown
3. Select "Monday" from Day dropdown
4. See filtered list with all details

**Result:**
```
| Module        | Lecturer  | Group | Day | Time  | Room   |
|---------------|-----------|-------|-----|-------|--------|
| Programming 1 | Mr. John  | IT 1  | Mon | 8–10  | Lab 1  |
| Mathematics   | Ms. Sarah | IT 1  | Mon | 10–12 | Room 3 |
```

---

## ✅ Benefits

### **Grid View:**
- Visual weekly overview
- Easy to spot free periods
- Good for planning
- Shows time conflicts

### **Table View:**
- All details visible
- Easy to filter
- Simple to print
- Clear lecturer assignments
- Good for reports

---

## 🔧 Customization

Both views support:
- ✅ Edit schedules
- ✅ Delete schedules
- ✅ Add new schedules
- ✅ Filter by division
- ✅ Filter by day (table view only)

---

**Choose the view that works best for your needs!**

**Grid View:** http://127.0.0.1:8000/academic/timetable
**Table View:** http://127.0.0.1:8000/academic/timetable/table
