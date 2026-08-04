<?php

namespace Tests\Unit;

use App\Services\SocialUrlParserService;
use PHPUnit\Framework\TestCase;

class SocialUrlParserServiceTest extends TestCase
{
    public function test_it_builds_contextual_comments_for_social_media_posts(): void
    {
        $service = new SocialUrlParserService();

        $comments = $service->buildContextualComments(
            'Pemkot Metro Percepat Pengerjaan Pengaspalan Jalan Sudirman',
            'Peningkatan infrastruktur jalan protokol Kota Metro',
            'Instagram'
        );

        $this->assertCount(4, $comments);
        $this->assertSame('Instagram', $comments[0]['platform']);
        $this->assertTrue(
            collect($comments)->contains(fn ($comment) => str_contains(strtolower($comment['comment']), 'metro'))
        );
    }
}
