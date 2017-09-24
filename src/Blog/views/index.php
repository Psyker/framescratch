<?= $renderer->render('header') ?>
<h1>Blog</h1>
<ul>
    <li><a href="<?= $router->generateUri('blog.show', ['slug' => 'first-post']) ?>">First post</a></li>
</ul>
<?= $renderer->render('footer') ?>
