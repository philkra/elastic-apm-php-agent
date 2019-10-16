<?php
namespace PhilKra\Tests\Helper;

use \PhilKra\Helper\Encoding;
use PhilKra\Tests\TestCase;

/**
 * Test Case for @see \PhilKra\Helper\Encoding
 */
final class EncodingTest extends TestCase {

    /**
     * @covers \PhilKra\Helper\Encoding::keywordField
     */
    public function testShortInput() {

        $input = "abcdefghijklmnopqrstuvwxyz1234567890";

        $this->assertEquals( $input, Encoding::keywordField($input) );

    }

    /**
     * @covers \PhilKra\Helper\Encoding::keywordField
     */
    public function testLongInput() {

        $input = str_repeat("abc123", 200);
        $output = str_repeat("abc123", 170) . 'abc' . '…';

        $this->assertEquals( $output, Encoding::keywordField($input) );

    }

    /**
     * @covers \PhilKra\Helper\Encoding::keywordField
     */
    public function testLongMultibyteInput() {

        $input = str_repeat("中国日本韓国合衆国", 200);
        $output = str_repeat("中国日本韓国合衆国", 113) . '中国日本韓国' . '…';

        $this->assertEquals( $output, Encoding::keywordField($input) );

    }

}
