# تحديث حالة المشروع تلقائياً بناءً على حالة المهام

## نظرة عامة

تم إضافة ميزة جديدة لتحديث حالة المشروع تلقائياً بناءً على حالة المهام الموجودة فيه. هذه الميزة تضمن أن حالة المشروع تعكس بدقة حالة المهام الموجودة فيه.

## كيفية العمل

### 1. تحديث تلقائي للحالة

عندما يتم تغيير حالة أي مهمة في المشروع، يتم تحديث حالة المشروع تلقائياً وفقاً للقواعد التالية:

- **مكتمل (Finished)**: عندما تكون جميع المهام في المشروع مكتملة
- **قيد العمل (Abuilding)**: عندما تكون هناك مهمة واحدة على الأقل قيد العمل
- **معلق (Pending)**: عندما تكون جميع المهام معلقة أو لم تبدأ بعد

### 2. القواعد التفصيلية

```php
// إذا لم تكن هناك مهام، اجعل المشروع معلق
if ($tasks->isEmpty()) {
    $project->status = 'Pending';
}

// إذا كانت جميع المهام مكتملة
$allCompleted = $tasks->every(fn($task) => $task->status === 'Completed');
if ($allCompleted) {
    $project->status = 'Finished';
}

// إذا كانت هناك مهمة واحدة على الأقل قيد العمل
$anyInProgress = $tasks->contains(fn($task) => $task->status === 'In Progress');
if ($anyInProgress) {
    $project->status = 'Abuilding';
}

// في الحالات الأخرى، اجعل المشروع معلق
$project->status = 'Pending';
```

## الملفات المحدثة

### 1. `app/Models/Project.php`
- إضافة علاقة `tasks()` مع المهام
- إضافة دالة `updateStatusBasedOnTasks()` لتحديث الحالة

### 2. `app/Observers/TaskObserver.php`
- تحديث `updated()` لتحديث حالة المشروع عند تغيير حالة المهمة
- إضافة `created()` لتحديث حالة المشروع عند إضافة مهمة جديدة
- إضافة `deleted()` لتحديث حالة المشروع عند حذف مهمة

### 3. `app/Http/Controllers/TaskController.php`
- إضافة دالة `updateProjectStatus()` لتحديث الحالة يدوياً

### 4. `app/Http/Controllers/ProjectController.php`
- إضافة دالة `updateProjectStatus()` لتحديث الحالة يدوياً
- إضافة دالة `getProjectStats()` لجلب إحصائيات المشروع

## المسارات الجديدة

### تحديث حالة المشروع
```http
POST /api/projects/{projectId}/update-status
```

**الاستجابة:**
```json
{
    "message": "تم تحديث حالة المشروع بنجاح",
    "project": {
        "id": 1,
        "title": "مشروع تجريبي",
        "status": "Abuilding"
    },
    "old_status": "Pending",
    "new_status": "Abuilding",
    "tasks_count": 5,
    "completed_tasks": 2,
    "in_progress_tasks": 1,
    "pending_tasks": 2
}
```

### جلب إحصائيات المشروع
```http
GET /api/projects/{projectId}/stats
```

**الاستجابة:**
```json
{
    "message": "تم جلب إحصائيات المشروع بنجاح",
    "project": {
        "id": 1,
        "title": "مشروع تجريبي",
        "status": "Abuilding"
    },
    "stats": {
        "total_tasks": 5,
        "completed_tasks": 2,
        "in_progress_tasks": 1,
        "pending_tasks": 2,
        "overdue_tasks": 0,
        "completion_percentage": 40.0,
        "project_status": "Abuilding"
    }
}
```

## الصلاحيات

- فقط مدير المشروع يمكنه تحديث حالة المشروع يدوياً
- التحديث التلقائي يحدث عند تغيير حالة أي مهمة

## أمثلة على الاستخدام

### مثال 1: مشروع بجميع المهام مكتملة
```php
// المهام: [Completed, Completed, Completed]
// حالة المشروع: Finished
```

### مثال 2: مشروع بمهام قيد العمل
```php
// المهام: [Completed, In Progress, Not Started]
// حالة المشروع: Abuilding
```

### مثال 3: مشروع بجميع المهام معلقة
```php
// المهام: [Not Started, Not Started, Not Started]
// حالة المشروع: Pending
```

## الفوائد

1. **دقة المعلومات**: حالة المشروع تعكس بدقة حالة المهام
2. **تحديث تلقائي**: لا حاجة للتدخل اليدوي
3. **شفافية**: يمكن رؤية تقدم المشروع بسهولة
4. **إدارة أفضل**: مساعدة في اتخاذ القرارات بناءً على الحالة الفعلية

## ملاحظات تقنية

- يتم استخدام Laravel Observers لمراقبة التغييرات
- يتم التحقق من الصلاحيات قبل التحديث اليدوي
- يتم إرجاع معلومات مفصلة عن التغييرات
- يدعم الإحصائيات التفصيلية للمشروع 