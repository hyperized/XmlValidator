<?php declare( strict_types=1 );

namespace Hyperized\Xml\Tests;

use Exception;
use Hyperized\Xml\Constants\Strings;
use Hyperized\Xml\Exceptions\FileDoesNotExist;
use Hyperized\Xml\Validator;
use PHPUnit\Framework\TestCase;
use function is_string;

/**
 * Class ValidatorTest
 *
 * @package Hyperized\Xml\Validator\Tests
 */
final class ValidatorTest extends TestCase {

	private static string $xsdFile = __DIR__ . '/files/simple.xsd';
	private static string $xmlFile = __DIR__ . '/files/correct.xml';
	private static string $incorrectXmlFile = __DIR__ . '/files/incorrect.xml';
	private static string $nonExistentFile = __DIR__ . '/files/does_not_exist.xml';
	private static string $emptyXmlFile = __DIR__ . '/files/empty.xml';
	private static string $version = Strings::VERSION;
	private static string $encoding = Strings::UTF_8;
	private Validator $validator;

	public function setUp(): void {
		$this->validator = new Validator();
	}

	/**
	 * Encoding validations
	 */
	public function testVersion(): void {
		$this->validator->setVersion( ValidatorTest::$version );
		self::assertEquals( ValidatorTest::$version, $this->validator->getVersion() );
	}

	public function testEncoding(): void {
		$this->validator->setEncoding( ValidatorTest::$encoding );
		self::assertEquals( ValidatorTest::$encoding, $this->validator->getEncoding() );
	}

	/**
	 * String validations
	 */
	public function testValidXMLString(): void {
		$contents = file_get_contents( ValidatorTest::$xmlFile );
		if ( is_string( $contents ) ) {
			self::assertTrue( $this->validator->isXMLStringValid( $contents ) );
		}
	}

	public function testInvalidXMLString(): void {
		$contents = file_get_contents( ValidatorTest::$incorrectXmlFile );
		if ( is_string( $contents ) ) {
			self::assertFalse( $this->validator->isXMLStringValid( $contents ) );
		}
	}

	public function testEmptyXMLString(): void {
		self::assertFalse( $this->validator->isXMLStringValid( '' ) );
	}

	/**
	 * File validations- XML
	 */
	public function testValidXMLFile(): void {
		self::assertTrue( $this->validator->isXMLFileValid( ValidatorTest::$xmlFile ) );
	}

	public function testNonExistentXmlFile(): void {
		self::assertFalse( $this->validator->isXMLFileValid( ValidatorTest::$nonExistentFile ) );
	}

	public function testFileGetContentsFalse(): void {
		stream_wrapper_register( 'invalid', InvalidStreamWrapper::class );
		self::assertFalse( @$this->validator->isXMLFileValid( 'invalid://foobar' ) );
	}

	public function testEmptyXmlFile(): void {
		self::assertFalse( $this->validator->isXMLFileValid( ValidatorTest::$emptyXmlFile ) );
	}

	public function testInvalidXMLFile(): void {
		self::assertFalse( $this->validator->isXMLFileValid( ValidatorTest::$incorrectXmlFile ) );
	}

	/**
	 * File validations- XML with XSD
	 */
	public function testValidXSDFile(): void {
		self::assertTrue( $this->validator->isXMLFileValid( ValidatorTest::$xmlFile, ValidatorTest::$xsdFile ) );
	}

	public function testNonExistentXSDFile(): void {
		self::assertFalse( $this->validator->isXMLFileValid( ValidatorTest::$xmlFile, ValidatorTest::$nonExistentFile ) );
	}

	public function testInvalidXSDFile(): void {
		self::assertFalse( $this->validator->isXMLFileValid( ValidatorTest::$incorrectXmlFile, ValidatorTest::$xsdFile ) );
	}

	/**
	 * Verify Exceptions
	 * @throws Exception
	 */
	public function testThrowError(): void {
		$this->expectException(FileDoesNotExist::class);
		$this->validator->isXMLFileValid( ValidatorTest::$xmlFile, ValidatorTest::$nonExistentFile);
		$this->validator->throwError();
	}
}
