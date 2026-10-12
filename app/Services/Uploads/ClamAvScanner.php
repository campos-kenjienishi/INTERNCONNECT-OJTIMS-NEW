<?php

namespace App\Services\Uploads;

use App\Exceptions\UnsafeUploadException;

class ClamAvScanner implements MalwareScanner
{
    public function __construct(
        private readonly string $host,
        private readonly int $port,
        private readonly float $timeout
    ) {}

    public function scan(string $path): MalwareScanResult
    {
        $socket = $this->connect();

        $file = @fopen($path, 'rb');

        if ($file === false) {
            fclose($socket);
            throw new UnsafeUploadException('file_unreadable', 'The uploaded file could not be inspected.');
        }

        try {
            $this->writeAll($socket, "zINSTREAM\0");

            while (! feof($file)) {
                $chunk = fread($file, 8192);

                if ($chunk === false) {
                    throw new UnsafeUploadException('file_unreadable', 'The uploaded file could not be inspected.');
                }

                if ($chunk !== '') {
                    $this->writeAll($socket, pack('N', strlen($chunk)).$chunk);
                }
            }

            $this->writeAll($socket, pack('N', 0));
            $response = $this->readResponse($socket);

            if (trim($response, "\0\r\n ") === '') {
                throw new UnsafeUploadException('scanner_error', 'The security scanner could not verify the uploaded file.');
            }

            $response = trim($response, "\0\r\n ");

            if (str_ends_with($response, ' OK')) {
                return MalwareScanResult::clean();
            }

            if (preg_match('/:\s*(.+)\s+FOUND$/', $response, $matches) === 1) {
                return MalwareScanResult::infected(trim($matches[1]));
            }

            throw new UnsafeUploadException('scanner_error', 'The security scanner could not verify the uploaded file.');
        } finally {
            fclose($file);
            fclose($socket);
        }
    }

    public function status(): MalwareScannerStatus
    {
        try {
            $socket = $this->connect();

            try {
                $this->writeAll($socket, "zPING\0");
                $response = trim($this->readResponse($socket), "\0\r\n ");
            } finally {
                fclose($socket);
            }

            if ($response !== 'PONG') {
                return MalwareScannerStatus::unavailable('ClamAV returned an unexpected health-check response.');
            }

            return MalwareScannerStatus::ready(sprintf('ClamAV is reachable at %s:%d.', $this->host, $this->port));
        } catch (UnsafeUploadException) {
            return MalwareScannerStatus::unavailable(sprintf('ClamAV is not reachable at %s:%d.', $this->host, $this->port));
        }
    }

    private function connect()
    {
        if (trim($this->host) === '' || $this->port < 1 || $this->port > 65535 || $this->timeout <= 0) {
            throw new UnsafeUploadException('scanner_configuration', 'The security scanner is not configured correctly.');
        }

        $errorCode = 0;
        $errorMessage = '';
        $socket = @stream_socket_client(
            sprintf('tcp://%s:%d', $this->host, $this->port),
            $errorCode,
            $errorMessage,
            $this->timeout
        );

        if ($socket === false) {
            throw new UnsafeUploadException('scanner_unavailable', 'The security scanner is temporarily unavailable. Please try again later.');
        }

        $seconds = (int) floor($this->timeout);
        $microseconds = (int) (($this->timeout - $seconds) * 1_000_000);
        stream_set_timeout($socket, $seconds, $microseconds);

        return $socket;
    }

    private function readResponse($socket): string
    {
        $response = '';

        while (! feof($socket)) {
            $character = fread($socket, 1);

            if ($character === false || $character === '') {
                $metadata = stream_get_meta_data($socket);

                if (($metadata['timed_out'] ?? false) === true) {
                    throw new UnsafeUploadException('scanner_error', 'The security scanner timed out while verifying the uploaded file.');
                }

                break;
            }

            if ($character === "\0") {
                break;
            }

            $response .= $character;

            if (strlen($response) > 4096) {
                throw new UnsafeUploadException('scanner_error', 'The security scanner returned an invalid response.');
            }
        }

        return $response;
    }

    private function writeAll($socket, string $data): void
    {
        $remaining = strlen($data);
        $offset = 0;

        while ($remaining > 0) {
            $written = fwrite($socket, substr($data, $offset));

            if ($written === false || $written === 0) {
                throw new UnsafeUploadException('scanner_error', 'The security scanner could not verify the uploaded file.');
            }

            $offset += $written;
            $remaining -= $written;
        }
    }
}
