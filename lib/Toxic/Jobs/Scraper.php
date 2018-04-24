<?php

namespace Toxic\Jobs;

use Toxic\Jobs\Messenger as JobsMessenger;

class Scraper {

    /**
     * Keywords to search with
     * @var array
     */
    private $keywords = [];

    /**
     * HTML output from the search
     * @var string
     */
    private $output;

    /**
     * Jobs for Sofia, posted today
     * @var string
     */
    private $url = 'https://www.jobs.bg/front_job_search.php?last=2&location_sid=1';
    private $db;

    public function __construct(array $keywords = []) {
        $this->keywords = $keywords;
    }

    /**
     * Search for jobs
     * @return $this
     */
    public function search() {

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->_buildUrl());

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $this->output = curl_exec($ch);

        curl_close($ch);

        return $this->_parseResponse();
    }

    private function _parseResponse() {
        $dom = \DOMDocument::loadHTML($this->output);

        $xpath = new \DOMXpath($dom);

        $jobs = $xpath->query("//a[@class='joblink']");
        $companies = $xpath->query("//a[@class='company_link']");

        $lines = [];

        if (is_null($jobs)) {
            return;
        }

        $this->_initDb();

        foreach ($jobs as $idx => $job) {

            $link = $job->getAttribute('href');
            preg_match('/\d+/', $link, $matches);

            $this->_saveJob($matches[0], $job->nodeValue, $companies[$idx]->nodeValue);
        }

        return $this->_sendMessage();
    }

    private function _sendMessage() {

        $date = new \DateTime();
        $date->sub(new \DateInterval('PT5M'));
        $before5 = $date->format('Y-m-d H:i:s');

        $query = $this->db->prepare("SELECT * FROM jobs where created_at > :before5 ORDER BY id desc");
        $query->execute(['before5' => $before5]);

        $jobs = $query->fetchAll(\PDO::FETCH_ASSOC);
        
        if(empty($jobs)){
            return;
        }
        
        $lines = [];
        
        foreach($jobs as $job) {
            $lines[] = $job['title'] . ' - ' . $job['company'] . "\r\nhttp://www.jobs.bg/job/" . $job['id'];
        }
        
        return JobsMessenger::sendMessage(implode("\r\n\r\n", $lines));
    }

    /**
     * Search URL with parameters and keywords
     * @return string
     */
    private function _buildUrl(): string {
        return $this->url . $this->_getKeywordsQuery();
    }

    /**
     * List of keywords into query string
     * @return string
     */
    private function _getKeywordsQuery(): string {

        if (empty($this->keywords)) {
            return;
        }


        $kw = [];

        foreach ($this->keywords as $keyword) {
            $kw[] = "keywords[]=" . $keyword;
        }

        return "&" . implode("&", $kw);
    }

    private function _initDb() {
        try {
            $this->db = new \PDO('mysql:dbname=' . $_SERVER['db.name'] . ';host=' . $_SERVER['db.host'] . ';charset=utf8', $_SERVER['db.user'], $_SERVER['db.password']);
        } catch (\PDOException $e) {
            error_log('Connection failed: ' . $e->getMessage());
            exit;
        }
    }

    private function _saveJob(int $id, string $title, string $company) {
        $query = $this->db->prepare("INSERT IGNORE INTO jobs values (:id, :title, :company, NOW())");
        $query->execute([
            ':id' => $id,
            ':title' => $title,
            ':company' => $company
        ]);
    }

}
