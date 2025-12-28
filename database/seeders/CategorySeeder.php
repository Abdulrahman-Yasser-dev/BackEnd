<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Skill;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name_en' => 'Design & Creative',
                'name_ar' => 'تصميم وإبداع',
                'type' => 'freelance',
                'skills' => [
                    ['name_en' => 'Logo Design', 'name_ar' => 'تصميم شعارات'],
                    ['name_en' => 'Graphic Design', 'name_ar' => 'تصميم جرافيك'],
                    ['name_en' => 'UI/UX Design', 'name_ar' => 'تصميم واجهات مستخدم'],
                    ['name_en' => 'Video Editing', 'name_ar' => 'مونتاج فيديو'],
                ]
            ],
            [
                'name_en' => 'Web & Programming',
                'name_ar' => 'برمجة وتطوير',
                'type' => 'freelance',
                'skills' => [
                    ['name_en' => 'Web Development', 'name_ar' => 'تطوير مواقع'],
                    ['name_en' => 'Mobile Apps', 'name_ar' => 'تطبيقات جوال'],
                    ['name_en' => 'Wordpress', 'name_ar' => 'ووردبريس'],
                    ['name_en' => 'Python', 'name_ar' => 'بايثون'],
                ]
            ],
            [
                'name_en' => 'Writing & Translation',
                'name_ar' => 'كتابة وترجمة',
                'type' => 'freelance',
                'skills' => [
                    ['name_en' => 'Content Writing', 'name_ar' => 'كتابة محتوى'],
                    ['name_en' => 'Translation', 'name_ar' => 'ترجمة'],
                    ['name_en' => 'Proofreading', 'name_ar' => 'تدقيق لغوي'],
                ]
            ],
            [
                'name_en' => 'Marketing & Sales',
                'name_ar' => 'تسويق ومبيعات',
                'type' => 'freelance',
                'skills' => [
                    ['name_en' => 'Digital Marketing', 'name_ar' => 'تسويق رقمي'],
                    ['name_en' => 'SEO', 'name_ar' => 'تحسين محركات البحث'],
                    ['name_en' => 'Social Media Math', 'name_ar' => 'إدارة حسابات تواصل'],
                ]
            ],
            // Local Services
            [
                'name_en' => 'Plumbing',
                'name_ar' => 'سباكة',
                'type' => 'local',
                'skills' => [
                    ['name_en' => 'Repair', 'name_ar' => 'صيانة وإصلاح'],
                    ['name_en' => 'Installation', 'name_ar' => 'تأسيس وتركيب'],
                ]
            ],
            [
                'name_en' => 'Electricity',
                'name_ar' => 'كهرباء',
                'type' => 'local',
                'skills' => [
                    ['name_en' => 'Home Electricity', 'name_ar' => 'كهرباء منازل'],
                    ['name_en' => 'Appliances', 'name_ar' => 'أجهزة كهربائية'],
                ]
            ],
            [
                'name_en' => 'Cleaning',
                'name_ar' => 'تنظيف',
                'type' => 'local',
                'skills' => [
                    ['name_en' => 'Home Cleaning', 'name_ar' => 'تنظيف منازل'],
                    ['name_en' => 'Office Cleaning', 'name_ar' => 'تنظيف مكاتب'],
                ]
            ],
            [
                'name_en' => 'Carpentry & Smithing',
                'name_ar' => 'نجارة وحدادة',
                'type' => 'local',
                'skills' => [
                    ['name_en' => 'Furniture', 'name_ar' => 'أثاث ومفروشات'],
                    ['name_en' => 'Doors & Windows', 'name_ar' => 'أبواب وشبابيك'],
                ]
            ],
            [
                'name_en' => 'Decoration & Gypsum',
                'name_ar' => 'ديكور وجبس',
                'type' => 'local',
                'skills' => [
                    ['name_en' => 'Interior Design', 'name_ar' => 'تصميم داخلي'],
                    ['name_en' => 'Gypsum Board', 'name_ar' => 'جبس بورد'],
                ]
            ],
            [
                'name_en' => 'Car Services',
                'name_ar' => 'خدمات سيارات',
                'type' => 'local',
                'skills' => [
                    ['name_en' => 'Mechanic', 'name_ar' => 'ميكانيكا'],
                    ['name_en' => 'Car Wash', 'name_ar' => 'غسيل وتلميع'],
                ]
            ],
        ];

        foreach ($categories as $catData) {
            $category = Category::create([
                'name_en' => $catData['name_en'],
                'name_ar' => $catData['name_ar'],
                'type' => $catData['type'] ?? 'freelance',
            ]);

            foreach ($catData['skills'] as $skillData) {
                Skill::create([
                    'category_id' => $category->id,
                    'name_en' => $skillData['name_en'],
                    'name_ar' => $skillData['name_ar'],
                ]);
            }
        }
    }
}
