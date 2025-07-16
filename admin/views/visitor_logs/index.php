<h1>Visitor Logs (Last 100)</h1>

<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Timestamp</th>
            <th>IP Address</th>
            <th>URL</th>
            <th>User Agent</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($logs)): ?>
            <tr><td colspan="4" class="text-center">No logs available</td></tr>
        <?php else: ?>
            <?php foreach ($logs as $log): ?>
                <tr>
                    <td><?= htmlspecialchars($log['timestamp']) ?></td>
                    <td><?= htmlspecialchars($log['ip']) ?></td>
                    <td><?= htmlspecialchars($log['url']) ?></td>
                    <td><?= htmlspecialchars($log['user_agent']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>